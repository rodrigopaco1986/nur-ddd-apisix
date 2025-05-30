<?php

return [

    'base_uri' => env('APISIX_ADMIN_URI', ''),
    'admin_key' => env('APISIX_ADMIN_KEY', ''),

    'routes' => [

        'login' => [
            'uri' => '/api/login',
            'methods' => ['POST'],
            'plugins' => [
                'proxy-rewrite' => [
                    'uri' => '/oauth/token',
                    'scheme' => 'https',
                ],
            ],
            'upstream' => [
                'type' => 'roundrobin',
                'nodes' => ['webserver:443' => 1],
                'scheme' => 'https',
            ],
        ],

        'protected-api' => [
            'uri' => '/api/*',
            'plugins' => [
                'jwt-auth' => [
                    'key_claim_name' => 'sub',
                    'algorithms' => ['RS256'],
                    'hide_credentials' => false,
                ],
            ],
            'upstream' => [
                'type' => 'roundrobin',
                'nodes' => ['webserver:443' => 1],
                'scheme' => 'https',
            ],
        ],

    ],
];
