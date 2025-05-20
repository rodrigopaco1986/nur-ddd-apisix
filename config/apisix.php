<?php

return [

    'base_uri'   => env('APISIX_ADMIN_URI', ''),
    'admin_key'  => env('APISIX_ADMIN_KEY', ''),

    'routes'     => [

        'login' => [
            'uri'     => '/api/login',
            'methods' => ['POST'],
            'plugins' => [
                'proxy-rewrite' => [
                    'uri' => '/oauth/token',
                    "scheme"=> "https",
                ],
            ],
            'upstream'=> [
                'type'  => 'roundrobin',
                'nodes' => ['webserver:443' => 1],
                "scheme"=> "https",
            ],
        ],

        'protected-api' => [
            'uri'     => '/api/*',
            'plugins' => [
                'jwt-auth' => [
                    'algorithm'  => 'RS256',
                    'public_key' => file_get_contents(storage_path('oauth-public.key')),
                    'key_claim_name' => 'sub',
                    "hide_credentials" => false,
                ],
            ],
            'upstream'=> [
                'type'  => 'roundrobin',
                'nodes' => ['webserver:443' => 1],
                "scheme"=> "https",
            ],
        ],

    ],
];
