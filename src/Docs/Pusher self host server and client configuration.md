# Pusher self host server and client configuration.

## First lets start with the pusher server configuration (Recommended to setup this in a live server for better performance)

1. (Create a fresh laravel instalation in you LOCAL machine, make sure you are using php 7.4 or beyond to bring the latest version of the pusher server)

2. ```php
   composer create-project laravel/laravel laravel-app
   ```

3. cd to you laravel-app

4. ```php
   composer require beyondcode/laravel-websockets
   ```

5.  At this point make sure you have a database setup with you laravel

6. ```bash
   php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider" --tag="migrations"
   ```

7. ```bash
   php artisan migrate
   ```

8. ```bash
   php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider" --tag="config"
   ```

### That complete the websocket installation now we need to test if the server work in localhost before we upload the the live server

1. You will need to update your .env file with the following

2. ```bash
   // We need those values so the pusher server know what to autenticate when the client try to user the api keys
   // Broad cast driver config very important
   BROADCAST_DRIVER=pusher
   // Pusher configuration
   PUSHER_APP_ID=12321321321312 // any random number
   PUSHER_APP_KEY=askdlasdksa // Any random string
   PUSHER_APP_SECRET=asdksalasdsa // Any random string
   PUSHER_APP_CLUSTER=mt1 // No need to change
   ```

3.  At this point you are read to test you local host version using this command to start the server

4. ```bash
   php artisan websockets:serve // Default port is 6001
   ```

5.  Go to /laravel-websockets and press connect

6.  if something is nor working you can follow the developer guide https://beyondco.de/docs/laravel-websockets/getting-started/introduction

## Now setting up the live server

1. zip your project and upload to you server make sure the laravel app is working fine

2. connect to the database and run a migration

3. Now if 

4. To setup the SSL you need to to the following

5. Inside the folder config you need to open the file websockets.php

6. inside this file you need to do the following changes

7. ```php
   // Inside the ssl array chagne thos values
   'ssl' => [
           /*
            * Path to local certificate file on filesystem. It must be a PEM encoded file which
            * contains your certificate and private key. It can optionally contain the
            * certificate chain of issuers. The private key also may be contained
            * in a separate file specified by local_pk.
            */
           'local_cert' => 'path/to/ssl_certificated.crt', // Path to the cetificated in the hestica cp
   
           /*
            * Path to local private key file on filesystem in case of separate files for
            * certificate (local_cert) and private key.
            */
           'local_pk' => 'path/to/ssl_certificated.key', // Path to the cetificated in the hestica cp
   
           /*
            * Passphrase with which your local_cert file was encoded.
            */
           'passphrase' => null,
   
           'verify_peer' => false,
       ],
   ```

8.  Now you need to setup your server client so you can connect to your pusher server and check the statistics using the ssl

9.  Inside the folder config you need to open the file broadcasting.php

10. And do the following

11. ```php
    // Chagne this 
    'pusher' => [
        'driver' => 'pusher',
        'key' => env('PUSHER_APP_KEY'),
        'secret' => env('PUSHER_APP_SECRET'),
        'app_id' => env('PUSHER_APP_ID'),
        'options' => [
            'cluster'   => env('PUSHER_APP_CLUSTER'),
            'encrypted' => true,
        ],
    ],
    // To note the options array has some now values
    'pusher' => [
        'driver' => 'pusher',
        'key' => env('PUSHER_APP_KEY'),
        'secret' => env('PUSHER_APP_SECRET'),
        'app_id' => env('PUSHER_APP_ID'),
        'options' => [
            'cluster'   => env('PUSHER_APP_CLUSTER'),
            'encrypted' => true, // Tel that is goin to encrypted
            'host'      => 'your_domain.com',// You domain without the https or http
            'port'      => 6001, // Port that we goin to use defult 6001
            'useTLS'    => true, // Use tls yes
            'scheme' => 'https', // if using ssl use the https
            'curl_options' => [ // Need to pass the ssl
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
            ],
        ],
    ],
    ```

## Now  if  you using linux you need a way to autostart the pusher server

1. ```bash
   // First, make sure supervisor is installed.
   # On Debian / Ubuntu
   apt install supervisor
   
   # On Red Hat / CentOS
   yum install supervisor
   systemctl enable supervisord
   ```

2.  Now we need to find the path where supervisor has been install and create a new file with your pusher config

3.  Once installed, add a new process that `supervisor` needs to keep running. You place your configurations in the `/etc/supervisor/conf.d` (Debian/Ubuntu) or `/etc/supervisord.d` (Red Hat/CentOS) directory.

4.  Within that directory, create a new file called `websockets.conf`.

5. ```bash
   [program:websockets]
   // Path to php        // Path to your laravel website root
   command=/usr/bin/php /home/laravel-echo/laravel-websockets/artisan websockets:serve
   numprocs=1
   autostart=true
   autorestart=true
   user=laravel-echo
   ```

6.  Once created, instruct `supervisor` to reload its configuration files (without impacting the already running `supervisor` jobs).

7. ```bash
   supervisorctl update // update the config
   supervisorctl start websockets // start the webscoket file configuration
   supervisorctl stop websockets // stop the webscoket file configuration
   ```

8. Now reboot your server so all the config can take place

## Now we going to setup a client site so when can talk to the server and the server talk with the front end

1. Ii am assuming you have a fresh laravel in you client site you need to instal this package

2. ```bash
   My pacakge name still nedd to create the package
   ```

3. I know laravel have a default websocket build in solution but this package use the official pusher php sdk so we can easily hookup with your hoster pusher server

4. Now in your .env file you need to change the following

5. ```bash
   PUSHER_APP_ID= 1234//Server we created pusher id
   PUSHER_APP_KEY=randonkey //Server we created pusher key
   PUSHER_APP_SECRET=randownsecret //Server we created pusher secret
   // Your server end point with now https or http // This is a varaible need for this pacakge only if you using package name
   PUSHER_HOST=mysite.com 
   PUSHER_APP_CLUSTER=mt1
   ```

6.  Those values we going to use in the pusher helper the package you just install using composer

7. ```bash
   namespace Mariojgt\Connect\Helpers;
   
   use Pusher\Pusher;
   
   class PusherHelper
   {
       // Source https://pusher.com/docs/channels/server_api/overview
       // you need to install "pusher/pusher-php-server": "^4.1",
       public function __construct()
       {
           $this->APP_KEY    = env('PUSHER_APP_KEY') ?? 'mariojgtrock';
           $this->APP_SECRET = env('PUSHER_APP_SECRET') ?? 'mariojgtsecret';
           $this->APP_ID     = env('PUSHER_APP_ID') ?? '1995';
   
           $this->pusher = new Pusher(
               $this->APP_KEY,
               $this->APP_SECRET,
               $this->APP_ID,
               array(
                   'cluster'      => env('PUSHER_APP_CLUSTER'),
                   'host'         => env('PUSHER_HOST') ?? 'yourhost.com', // This is Important if not using the official
                   'port'         => 6001, // Portal defaul 443
                   'useTLS'       => true,
                   'encrypted'    => true, // if without ssl them false
                   'scheme'       => 'https', // Typo of connection https if using ssl
                   'curl_options' => [
                       CURLOPT_SSL_VERIFYHOST => 0, // Required to work with ssl
                       CURLOPT_SSL_VERIFYPEER => 0, // Required to work with ssl
                   ],
               )
           );
       }
   
       public function send()
       {
           $data = $this->pusher->trigger('my-channel', 'my-event', array('message' => 'data'));
   
           if ($data) {
               return ['status' => true];
           } else {
               return ['status' => false];
           }
       }
   
       public function sendBatch()
       {
           // Example
           $batch = [];
           $batch[] = [
               'channel' => 'my-channel-1',
               'name'    => 'my-event-1',
               'data'    => ['hello' => 'world']
           ];
   
           $data =  $this->pusher->triggerBatch($batch);
   
           if ($data) {
               return ['status' => true];
           } else {
               return ['status' => false];
           }
       }
   
       public function multipleChannel()
       {
           $data = $this->pusher->trigger(
               [
                   'my-channel-1',
                   'my-channel-2',
                   'my-channel-3'
               ],
               'my-event',
               [
                   'message' => 'hello world',
                   'message' => 'hello world2',
                   'message' => 'hello world3',
               ]
           );
   
           if ($data) {
               return ['status' => true];
           } else {
               return ['status' => false];
           }
       }
}
   ```
   
8. Now we need to set up the front end to listen to your self hosted pusher

9. More info https://laravel.com/docs/8.x/broadcasting#client-side-installation

10. in you npm do the following

11. ```bash
    // This will isntall the pusher js and laravel echo
    npm install --save-dev laravel-echo pusher-js
    ```

12. Now in you app.js file you need to tell echo to use you pusher server configurations 

13. ```js
    /**
     * Echo exposes an expressive API for subscribing to channels and listening
     * for events that are broadcast by Laravel. Echo and event broadcasting
     * allows your team to easily build robust real-time web applications.
     */
    
    import Echo from 'laravel-echo';
    
    window.Pusher = require('pusher-js');
    
    window.Echo = new Echo({
        broadcaster: 'pusher', // Dont change this
        key: 'mariojgtrock', // You self hoster pusher server key
        wsHost: 'example.com', // You self hoster pusher server host with out the https or http
        wsPort: 6001, // server allowed port in you server
        forceTLS: false, // don't force tls other wise will not connect to the server
        disableStats: true,
    });
    
    ```

14. now a example how you can use in the front end

15. ```php
    // Note the ('.') inside the .listen if you remvoe the ('.') will now work
    // Now you can use you self hosted dashboard to send even to you site fron end
    Echo.channel(`my-channel`)
        .listen('.my-event', (pusher_response) => {
            console.log(pusher_response)
        });
    ```

16. Example to to trigger a event using the package name helper class

17. ```php
    // in you controller you can send the event to you pusher serve doing this
    
    use namespace Mariojgt\Connect\Helpers\PusherHelper;
    
    public function pusherSendEvent () {
        $pusherManager = new PusherHelper();
        $pusherManager->send('chanel_name', 'event_name', ['data' => 'to_send'])
    }
    ```

18. the official pusher php documentation https://pusher.com/docs/channels/server_api/overview 

19. That complete you pusher server setup

## Help References:

1. Websocket server setup https://beyondco.de/docs/laravel-websockets/getting-started/introduction
2. Laravel echo setup https://laravel.com/docs/8.x/broadcasting