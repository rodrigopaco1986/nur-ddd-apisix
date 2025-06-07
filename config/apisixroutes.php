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
                //'proxy-rewrite' => [SOME REWRITE RULE THAT OVERWRITES THE GENERAL ONE],
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
        ]
    ],
    //'clientes' => []
];