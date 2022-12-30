<?php

return [
    'paths' => ['api/*', 'oauth/*', 'api/broadcasting/auth'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['https://app.kazibin.adilirealestate.com', 'http://localhost:8080', 'https://app.kazibin.com'],

    'allowed_origins_patterns' => ['*'],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 1000,

    'supports_credentials' => false,

];
