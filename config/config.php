<?php

return [
    'sms' => [
        'base_url' => env('SMS_BASE_URL', 'https://sms.fake.uz/api'),
        'token' => env('SMS_TOKEN'),
        'telegram_bot_token' => env('SMS_TELEGRAM_BOT_TOKEN'),
        'telegram_chats' => env('SMS_TELEGRAM_CHATS'),
    ],
    'firebase' => [
        'base_url' => env('SMS_BASE_URL', 'https://sms.fake.uz/api'),
        'token' => env('FIREBASE_TOKEN'),
        'telegram_bot_token' => env('SMS_TELEGRAM_BOT_TOKEN'),
        'telegram_chats' => env('SMS_TELEGRAM_CHATS'),
    ],
    'media' => [
        'base_url' => env('MEDIA_BASE_URL', 'https://media.fake.uz/api'),
        'token' => env('MEDIA_TOKEN'),
    ],
    'crm' => [
        'base_url' => env('CRM_BASE_URL', 'https://crm.fake.uz/api'),
    ],
    'helpdesk' => [
        'base_url' => env('HELPDESK_BASE_URL', 'https://helpdesk.fake.uz/api'),
    ],
    'products' => [
        'base_url' => env('PRODUCTS_BASE_URL', 'https://products.fake.uz/api'),
    ],
    'one_c' => [
        'base_url' => env('ONE_C_BASE_URL'),
    ],
    'api_gateway' => [
        'base_url' => env('API_GATEWAY_BASE_URL', 'https://gateway.fake.uz/api'),
    ],
    'rating' => [
        'base_url' => env('RATING_BASE_URL', 'https://rating.fake.uz/api'),
    ],
    'service_crm' => [
        'base_url' => env('SERVICE_CRM_BASE_URL', 'https://service-crm.fake.uz/api'),
    ],

    'cache' => env('HTTP_CLIENT_CACHE', false),
];
