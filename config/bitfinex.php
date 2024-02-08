<?php

return [
    'authBaseUrl' => 'https://api.bitfinex.com',
    'pubBaseUrl' => 'https://api-pub.bitfinex.com',
    'alert_email' => env('BITFINEX_ALERT_EMAIL', 'email@example.com'),
    'alert_name' => env('BITFINEX_ALERT_NAME', 'Bitfinex'),
    'apiKey' => env('BITFINEX_API_KEY', ''),
    'apiSecret' => env('BITFINEX_API_SECRET', ''),
];
