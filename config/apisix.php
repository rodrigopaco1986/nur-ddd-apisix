<?php

$routesConfig = require_once __DIR__ . '/apisixroutes.php';

$config = [

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

        'protected-auth-api' => [
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

$routesList = collect($routesConfig)->map(function ($item, $key) {

    return collect($item['uris'])->mapWithKeys(function ($subItem, $subKey) use ($item) {
        $uri = [
            'uri'     => $subItem['uri'],
            'methods' => [$subItem['method']],
            'plugins' => [
                'jwt-auth' => [
                    'key_claim_name'   => 'sub',
                    'algorithms'       => ['RS256'],
                    'hide_credentials' => false,
                ],
            ],
            'upstream' => [
                'type'   => 'roundrobin',
                'nodes'  => [$item['node'] => 1],
                'scheme' => $item['scheme'],
            ],
        ];

        $gralRewrite = $item['proxy-rewrite'] ?? false;
        $customRewrite = $item['proxy-rewrite'] ?? false;
        if ($gralRewrite || $customRewrite) {
            $uri['plugins']['proxy-rewrite'] = $customRewrite ? $customRewrite : $gralRewrite;
        }

        $public = $subItem['public'] ?? false;
        if ($public === true) {
            unset($uri['plugins']['jwt-auth']);
        }

        return [$subItem['name'] => $uri];

    })->toArray();

})
->collapse()
->all();

$config['routes'] = array_merge($config['routes'], $routesList);

return $config;