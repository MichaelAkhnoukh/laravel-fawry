<?php


return [
    'staging_url' => env('FAWRYPAY_STAGING_URL', 'https://atfawry.fawrystaging.com/ECommerceWeb/Fawry/'),

    'production_url' => env('FAWRYPAY_PRODUCTION_URL', 'https://www.atfawry.com/ECommerceWeb/Fawry/'),

    'environment' => env('FAWRYPAY_ENV', 'staging'),

    'merchant_code' => env('FAWRYPAY_MERCHANT_CODE'),

    'security_key' => env('FAWRYPAY_SECURITY_KEY'),

    'endpoints' => [
        'card_tokenization' => [
            'method' => 'POST',
            'uri' => 'cards/cardToken',
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ]
        ]
    ]
];