<?php

return [
    'api_key'   => env('TERMII_API_KEY'),
    'sender_id' => env('TERMII_SENDER_ID', 'BUAA'),
    'base_url'  => env('TERMII_BASE_URL', 'https://v3.api.termii.com/api'),
    'channel'   => env('TERMII_CHANNEL', 'generic'),
];
