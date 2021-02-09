<?php

namespace Mariojgt\Connect\Helpers;

use Pusher\Pusher;

class PusherHelper
{
    // Source https://pusher.com/docs/channels/server_api/overview
    // you need to install "pusher/pusher-php-server": "^4.1",
    public function __construct()
    {
        $this->APP_KEY    = env('PUSHER_APP_ID') ?? 'random_key';
        $this->APP_SECRET = env('PUSHER_APP_KEY') ?? 'random_secret';
        $this->APP_ID     = env('PUSHER_APP_SECRET') ?? 'random_app_id';

        // The pusher required start up
        $this->pusher = new Pusher(
            $this->APP_KEY,
            $this->APP_SECRET,
            $this->APP_ID,
            array(
                'cluster'      => env('PUSHER_APP_CLUSTER'),
                // PUSHER_HOST you may need to add in you env file is required for this package
                'host'         => env('PUSHER_HOST') ?? 'yourhost.com', // This is Important if not using the official
                'port'         => 6001, // Portal defaul 443
                'useTLS'       => true,
                'encrypted'    => true, // if without ssl them false
                'scheme'       => 'https', // Type of connection
                'curl_options' => [
                    CURLOPT_SSL_VERIFYHOST => 0, // Required to work with ssl
                    CURLOPT_SSL_VERIFYPEER => 0, // Required to work with ssl
                ],
            )
        );
    }
    // Example hwo to send a event to the pusher you must have a laravel echo setup so listener to event
    public function send($chanel, $event, $dataToSend)
    {
        // Send the data false if something when worng true if success
        $data = $this->pusher->trigger($chanel, $event, $dataToSend);

        if ($data) {
            return ['status' => true];
        } else {
            return ['status' => false];
        }
    }
    // Example
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
    // Example
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
