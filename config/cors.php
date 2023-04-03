<?php

return [
    'paths' => ['api/*', 'oauth/*', 'api/broadcasting/auth'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['https://app.kazibin.com', 'https://kazibin.com', 'http://localhost:8080', 'http://127.0.0.1:8080', 'http://localhost'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 1000,

    'supports_credentials' => false,

];
