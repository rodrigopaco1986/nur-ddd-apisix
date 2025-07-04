<?php

return [
    'sales' => [
        'node' => 'sales-webserver:80',
        'scheme' => 'http',
        'proxy-rewrite' => ['regex_uri' => ['^/sales/(.*)', '/$1']],
        'uris' => [
            [
                'name' => 'protected-sales-order-create-api',
                'uri' => '/sales/order/create',
                'method' => 'POST',
                // 'proxy-rewrite' => [SOME REWRITE RULE THAT OVERWRITES THE GENERAL ONE],
            ],
            [
                'name' => 'protected-sales-order-view-api',
                'uri' => '/sales/order/view/*',
                'method' => 'GET',
            ],
            [
                'name' => 'protected-sales-invoice-create-api',
                'uri' => '/sales/invoice/create',
                'method' => 'POST',
            ],
            [
                'name' => 'protected-sales-invoice-view-api',
                'uri' => '/sales/invoice/view/*',
                'method' => 'GET',
            ],
            [
                'name' => 'protected-sales-payment-make-api',
                'uri' => '/sales/payment/make',
                'method' => 'POST',
            ],
            [
                'name' => 'protected-sales-payment-view-api',
                'uri' => '/sales/payment/view-by-order/*',
                'method' => 'POST',
            ],
            [
                'name' => 'protected-sales-telescope',
                'uri' => '/sales/telescope',
                'method' => 'GET',
                'public' => true,
            ],
        ],
    ],
    'services' => [
        'node' => 'service-webserver:80',
        'scheme' => 'http',
        'proxy-rewrite' => ['regex_uri' => ['^/services/(.*)', '/$1']],
        'uris' => [
            [
                'name' => 'protected-services-service-list-api',
                'uri' => '/services/service',
                'method' => 'GET',
            ],
            [
                'name' => 'protected-services-service-create-api',
                'uri' => '/services/service/create',
                'method' => 'POST',
            ],
            [
                'name' => 'protected-services-service-view-api',
                'uri' => '/services/service/*',
                'method' => 'GET',
            ],
            [
                'name' => 'protected-services-service-update-api',
                'uri' => '/services/service/*',
                'method' => 'PUT',
            ],
            [
                'name' => 'protected-services-service-delete-api',
                'uri' => '/services/service/*',
                'method' => 'DELETE',
            ],
            [
                'name' => 'protected-services-telescope',
                'uri' => '/services/telescope',
                'method' => 'GET',
                'public' => true,
            ],
        ],
    ],
];
