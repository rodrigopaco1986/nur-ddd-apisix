<?php

$routesConfig = require_once __DIR__.'/apisixroutes.php';
$otelConfigs = [
    'opentelemetry' => [
        'sampler' => [
            'name' => 'always_on',
        ],
        'endpoint' => 'http://otel-collector:4318/v1/traces',
    ],
    'http-logger' => [
        'uri' => 'http://otel-collector:4318/v1/logs',
        'batch_max_size' => 1,
        'buffer_duration' => 60,
        'log_format' => [
            'host' => '$hostname',
            'client_ip' => '$remote_addr',
            'method' => '$request_method',
            'uri' => '$uri',
            'status' => '$status',
            'latency_ms' => '$latency * 1000',
            'service_name' => '$service_name',
            'route_name' => '$route_name',
        ],
    ],
];

$config = [

    'base_uri' => env('APISIX_ADMIN_URI', ''),
    'admin_key' => env('APISIX_ADMIN_KEY', ''),

    /**
     * Define plugins that should be enabled globally on APISIX.
     * The key is the plugin name, and the value is its configuration.
     * An empty object means enable with default settings.
     */
    'global_rules' => [
        '1' => [
            'plugins' => [
                'prometheus' => new \stdClass,
            ],
        ],
    ],

    'routes' => [

        'login' => [
            'uri' => '/api/login',
            'methods' => ['POST'],
            'plugins' => array_merge(
                [
                    'proxy-rewrite' => [
                        'uri' => '/api/auth/token',
                        'scheme' => 'https',
                    ],
                ],
                $otelConfigs,
            ),
            'upstream' => [
                'type' => 'roundrobin',
                'nodes' => ['webserver:443' => 1],
                'scheme' => 'https',
            ],
        ],

        'login-oauth' => [
            'uri' => '/api/login-oauth',
            'methods' => ['POST'],
            'plugins' => array_merge(
                [
                    'proxy-rewrite' => [
                        'uri' => '/oauth/token',
                        'scheme' => 'https',
                    ],
                ],
                $otelConfigs,
            ),
            'upstream' => [
                'type' => 'roundrobin',
                'nodes' => ['webserver:443' => 1],
                'scheme' => 'https',
            ],
        ],

        'protected-auth-api' => [
            'uri' => '/api/*',
            'plugins' => array_merge(
                [
                    'jwt-auth' => [
                        'key_claim_name' => 'sub',
                        'algorithms' => ['RS256'],
                        'hide_credentials' => false,
                    ],
                ],
                $otelConfigs,
            ),
            'upstream' => [
                'type' => 'roundrobin',
                'nodes' => ['webserver:443' => 1],
                'scheme' => 'https',
            ],
        ],
    ],
];

$routesList = collect($routesConfig)->map(function ($item, $key) use ($otelConfigs) {

    return collect($item['uris'])->mapWithKeys(function ($subItem, $subKey) use ($item, $otelConfigs) {
        $uri = [
            'uri' => $subItem['uri'],
            'methods' => [$subItem['method']],
            'plugins' => array_merge(
                [
                    'jwt-auth' => [
                        'key_claim_name' => 'sub',
                        'algorithms' => ['RS256'],
                        'hide_credentials' => false,
                    ],

                ],
                $otelConfigs,
            ),
            'upstream' => [
                'type' => 'roundrobin',
                'nodes' => [$item['node'] => 1],
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
