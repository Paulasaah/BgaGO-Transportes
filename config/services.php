<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Servicios de la APP - Configuración de servicios externos
    |--------------------------------------------------------------------------
    |
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | HTTP Client Timeouts
    |--------------------------------------------------------------------------
    | Configuración de timeouts para llamadas HTTP externas
    */
    'http' => [
        'timeout' => env('HTTP_TIMEOUT', 30), // 30 segundos timeout general
        'connect_timeout' => env('HTTP_CONNECT_TIMEOUT', 10), // 10 segundos para conectar
        'retry' => [
            'times' => env('HTTP_RETRY_TIMES', 3), // 3 intentos
            'sleep' => env('HTTP_RETRY_SLEEP', 1000), // 1 segundo entre intentos (ms)
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Mercado Pago 
    |--------------------------------------------------------------------------
    */
    'mercadopago' => [
        'access_token' => env('MERCADOPAGO_ACCESS_TOKEN'),
        'public_key' => env('MERCADOPAGO_PUBLIC_KEY'),
        'timeout' => env('MERCADOPAGO_TIMEOUT', 15), // 15 segundos timeout
        'webhook_secret' => env('MERCADOPAGO_WEBHOOK_SECRET'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Azure IoT 
    |--------------------------------------------------------------------------
    */
    'azure_iot' => [
        'connection_string' => env('AZURE_IOT_CONNECTION_STRING'),
        'timeout' => env('AZURE_IOT_TIMEOUT', 10), // 10 segundos timeout
    ],

    /*
    |--------------------------------------------------------------------------
    | OSRM 
    |--------------------------------------------------------------------------
    */

    'osrm' => [
        'host' => env('OSRM_HOST'),
    ],


];
