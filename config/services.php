<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    // ✅ Blockchain Bridge
    'blockchain' => [
        'bridge_url' => env('BLOCKCHAIN_BRIDGE_URL', 'http://localhost:3001'),
        'api_secret' => env('BLOCKCHAIN_API_SECRET', ''),
        'contract'   => env('BLOCKCHAIN_CONTRACT_ADDRESS', ''),
        'network'    => env('BLOCKCHAIN_NETWORK', 'polygon-amoy'),
    ],

];