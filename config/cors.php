<?php

return [
    'paths' => ['api/*', 'oauth/*', 'api/broadcasting/auth'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'https://app.kazibin.com', 'https://kazibin.com', 'https://www.kazibin.com', 
        'http://localhost:8080', 'http://127.0.0.1:8080', 'http://localhost', 
        'http://192.168.1.101:8080', 'http://192.168.1.4:8080', 'https://kazibin.adilirealestate.com',
        'https://landing.kazibin.adilirealestate.com', 'http://192.168.1.5:8080'
    ],

    'allowed_origins_patterns' => ['*'],

    'allowed_headers' => ['Origin', 'Content-Type', 'X-Auth-Token', 'Authorization', 'Accept', 'charset', 'boundary', 'Content-Length'],

    'exposed_headers' => [],

    'max_age' => 10000,

    'supports_credentials' => false,

];
