<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://' . env('FRONTEND_BUAA'),
        'https://' . env('FRONTEND_BUAA'),
        'https://' . env('FRONTEND_BUAA_WWW'),
    ],



    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
