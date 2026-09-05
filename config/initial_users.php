<?php

return [
    'admin' => [
        'name' => env('INITIAL_ADMIN_NAME', 'System Administrator'),
        'email' => env('INITIAL_ADMIN_EMAIL', 'admin@example.com'),
        'phone' => env('INITIAL_ADMIN_PHONE', '+968 9000 0000'),
        'password' => env('INITIAL_ADMIN_PASSWORD'),
    ],
    'sales' => [
        'name' => env('INITIAL_SALES_NAME', 'Muscat Sales'),
        'email' => env('INITIAL_SALES_EMAIL', 'sales@example.com'),
        'phone' => env('INITIAL_SALES_PHONE', '+968 9000 0001'),
        'password' => env('INITIAL_SALES_PASSWORD'),
    ],
    'pricing' => [
        'name' => env('INITIAL_PRICING_NAME', 'Dubai Pricing'),
        'email' => env('INITIAL_PRICING_EMAIL', 'pricing@example.com'),
        'phone' => env('INITIAL_PRICING_PHONE', '+971 50 000 0000'),
        'password' => env('INITIAL_PRICING_PASSWORD'),
    ],
];
