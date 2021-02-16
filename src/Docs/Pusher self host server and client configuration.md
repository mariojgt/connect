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

8. Example Complete websockets.php file setup

9. ```php
   <?php
   
   return [
   
       /*
       |--------------------------------------------------------------------------
       | Dashboard Settings
       |--------------------------------------------------------------------------
       |
       | You can configure the dashboard settings from here.
       |
       */
   
       'dashboard' => [
   
           'port' => env('LARAVEL_WEBSOCKETS_PORT', 6001),
   
           'path' => 'my-dashboard',// Path to acess the dashboard
   
           'middleware' => [
               'web',
               'auth',
               //\BeyondCode\LaravelWebSockets\Dashboard\Http\Middleware\Authorize::class,
           ],
   
       ],
   
       'managers' => [
   
           /*
           |--------------------------------------------------------------------------
           | Application Manager
           |--------------------------------------------------------------------------
           |
           | An Application manager determines how your websocket server allows
           | the use of the TCP protocol based on, for example, a list of allowed
           | applications.
           | By default, it uses the defined array in the config file, but you can
           | anytime implement the same interface as the class and add your own
           | custom method to retrieve the apps.
           |
           */
   
           'app' => \BeyondCode\LaravelWebSockets\Apps\ConfigAppManager::class,
   
       ],
   
       /*
       |--------------------------------------------------------------------------
       | Applications Repository
       |--------------------------------------------------------------------------
       |
       | By default, the only allowed app is the one you define with
       | your PUSHER_* variables from .env.
       | You can configure to use multiple apps if you need to, or use
       | a custom App Manager that will handle the apps from a database, per se.
       |
       | You can apply multiple settings, like the maximum capacity, enable
       | client-to-client messages or statistics.
       |
       */
   
       'apps' => [
           [
               'id'                     => env('PUSHER_APP_ID'),
               'name'                   => env('APP_NAME'),
               'host'                   => env('PUSHER_APP_HOST'),
               'key'                    => env('PUSHER_APP_KEY'),
               'secret'                 => env('PUSHER_APP_SECRET'),
               'path'                   => env('PUSHER_APP_PATH'),
               'capacity'               => null,
               'enable_client_messages' => false,
               'enable_statistics'      => true,
               'allowed_origins'        => [
                   //
               ],
           ],
       ],
   
       /*
       |--------------------------------------------------------------------------
       | Broadcasting Replication PubSub
       |--------------------------------------------------------------------------
       |
       | You can enable replication to publish and subscribe to
       | messages across the driver.
       |
       | By default, it is set to 'local', but you can configure it to use drivers
       | like Redis to ensure connection between multiple instances of
       | WebSocket servers. Just set the driver to 'redis' to enable the PubSub using Redis.
       |
       */
   
       'replication' => [
   
           'mode' => env('WEBSOCKETS_REPLICATION_MODE', 'local'),
   
           'modes' => [
   
               /*
               |--------------------------------------------------------------------------
               | Local Replication
               |--------------------------------------------------------------------------
               |
               | Local replication is actually a null replicator, meaning that it
               | is the default behaviour of storing the connections into an array.
               |
               */
   
               'local' => [
   
                   /*
                   |--------------------------------------------------------------------------
                   | Channel Manager
                   |--------------------------------------------------------------------------
                   |
                   | The channel manager is responsible for storing, tracking and retrieving
                   | the channels as long as their members and connections.
                   |
                   */
   
                   'channel_manager' => \BeyondCode\LaravelWebSockets\ChannelManagers\LocalChannelManager::class,
   
                   /*
                   |--------------------------------------------------------------------------
                   | Statistics Collector
                   |--------------------------------------------------------------------------
                   |
                   | The Statistics Collector will, by default, handle the incoming statistics,
                   | storing them until they will become dumped into another database, usually
                   | a MySQL database or a time-series database.
                   |
                   */
   
                   'collector' => \BeyondCode\LaravelWebSockets\Statistics\Collectors\MemoryCollector::class,
   
               ],
   
               'redis' => [
   
                   'connection' => env('WEBSOCKETS_REDIS_REPLICATION_CONNECTION', 'default'),
   
                   /*
                   |--------------------------------------------------------------------------
                   | Channel Manager
                   |--------------------------------------------------------------------------
                   |
                   | The channel manager is responsible for storing, tracking and retrieving
                   | the channels as long as their members and connections.
                   |
                   */
   
                   'channel_manager' => \BeyondCode\LaravelWebSockets\ChannelManagers\RedisChannelManager::class,
   
                   /*
                   |--------------------------------------------------------------------------
                   | Statistics Collector
                   |--------------------------------------------------------------------------
                   |
                   | The Statistics Collector will, by default, handle the incoming statistics,
                   | storing them until they will become dumped into another database, usually
                   | a MySQL database or a time-series database.
                   |
                   */
   
                   'collector' => \BeyondCode\LaravelWebSockets\Statistics\Collectors\RedisCollector::class,
   
               ],
   
           ],
   
       ],
   
       'statistics' => [
   
           /*
           |--------------------------------------------------------------------------
           | Statistics Store
           |--------------------------------------------------------------------------
           |
           | The Statistics Store is the place where all the temporary stats will
           | be dumped. This is a much reliable store and will be used to display
           | graphs or handle it later on your app.
           |
           */
   
           'store' => \BeyondCode\LaravelWebSockets\Statistics\Stores\DatabaseStore::class,
   
           /*
           |--------------------------------------------------------------------------
           | Statistics Interval Period
           |--------------------------------------------------------------------------
           |
           | Here you can specify the interval in seconds at which
           | statistics should be logged.
           |
           */
   
           'interval_in_seconds' => 60,
   
           /*
           |--------------------------------------------------------------------------
           | Statistics Deletion Period
           |--------------------------------------------------------------------------
           |
           | When the clean-command is executed, all recorded statistics older than
           | the number of days specified here will be deleted.
           |
           */
   
           'delete_statistics_older_than_days' => 60,
   
       ],
   
       /*
       |--------------------------------------------------------------------------
       | Maximum Request Size
       |--------------------------------------------------------------------------
       |
       | The maximum request size in kilobytes that is allowed for
       | an incoming WebSocket request.
       |
       */
   
       'max_request_size_in_kb' => 250,
   
       /*
       |--------------------------------------------------------------------------
       | SSL Configuration
       |--------------------------------------------------------------------------
       |
       | By default, the configuration allows only on HTTP. For SSL, you need
       | to set up the the certificate, the key, and optionally, the passphrase
       | for the private key.
       | You will need to restart the server for the settings to take place.
       |
       */
   
       'ssl' => [
           /*
            * Path to local certificate file on filesystem. It must be a PEM encoded file which
            * contains your certificate and private key. It can optionally contain the
            * certificate chain of issuers. The private key also may be contained
            * in a separate file specified by local_pk.
            */
           'local_cert' => 'certificate.net.crt',
   
           /*
            * Path to local private key file on filesystem in case of separate files for
            * certificate (local_cert) and private key.
            */
           'local_pk' => 'certificate.net.key',
   
           /*
            * Passphrase with which your local_cert file was encoded.
            */
           'passphrase' => null,
   
           'verify_peer' => false,
       ],
   
       /*
       |--------------------------------------------------------------------------
       | Route Handlers
       |--------------------------------------------------------------------------
       |
       | Here you can specify the route handlers that will take over
       | the incoming/outgoing websocket connections. You can extend the
       | original class and implement your own logic, alongside
       | with the existing logic.
       |
       */
   
       'handlers' => [
   
           'websocket' => \BeyondCode\LaravelWebSockets\Server\WebSocketHandler::class,
   
           'health' => \BeyondCode\LaravelWebSockets\Server\HealthHandler::class,
   
           'trigger_event' => \BeyondCode\LaravelWebSockets\API\TriggerEvent::class,
   
           'fetch_channels' => \BeyondCode\LaravelWebSockets\API\FetchChannels::class,
   
           'fetch_channel' => \BeyondCode\LaravelWebSockets\API\FetchChannel::class,
   
           'fetch_users' => \BeyondCode\LaravelWebSockets\API\FetchUsers::class,
   
       ],
   
       /*
       |--------------------------------------------------------------------------
       | Promise Resolver
       |--------------------------------------------------------------------------
       |
       | The promise resolver is a class that takes a input value and is
       | able to make sure the PHP code runs async by using ->then(). You can
       | use your own Promise Resolver. This is usually changed when you want to
       | intercept values by the promises throughout the app, like in testing
       | to switch from async to sync.
       |
       */
   
       'promise_resolver' => \React\Promise\FulfilledPromise::class,
   
   ];
   
   ```

   

10. Now you need to setup your server client so you can connect to your pusher server and check the statistics using the ssl

11. Inside the folder config you need to open the file broadcasting.php

12. And do the following

13. ```php
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
   composer require mariojgt/connect
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

16. Example to to trigger a event using the mariojgt/connect helper class

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