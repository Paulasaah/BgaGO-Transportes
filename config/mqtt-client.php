<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Conexiones MQTT disponibles
    |--------------------------------------------------------------------------
    | Aquí defines las configuraciones de conexión que usará tu app. 
    | Puedes definir varias y elegir una por defecto con MQTT_DEFAULT_CONNECTION.
    |--------------------------------------------------------------------------
    */

    'default_connection' => env('MQTT_DEFAULT_CONNECTION', 'local'),

    'connections' => [

        'local' => [
            'host'        => env('MQTT_HOST', '127.0.0.1'),
            'port'        => env('MQTT_PORT', 1883),
            'username'    => env('MQTT_USERNAME', null),
            'password'    => env('MQTT_PASSWORD', null),
            'client_id'   => env('MQTT_CLIENT_ID', 'laravel-listener-' . uniqid()),
            'clean_session' => true,
            'logging'     => true,

            // TLS
            'use_tls'     => env('MQTT_TLS_ENABLED', false),
            'tls'         => [
                'verify_peer' => false,
                'allow_self_signed' => true,
                'verify_peer_name' => false,
            ],

            // Keep alive y timeout
            'connect_timeout' => 5,
            'keep_alive_interval' => 60,
        ],
    ],

];
