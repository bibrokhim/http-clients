<?php

return [
    'sms' => [
        'base_url' => env('SMS_BASE_URL'),
        'token' => env('SMS_TOKEN'),
        'telegram_bot_token' => env('SMS_TELEGRAM_BOT_TOKEN'),
        'telegram_chats' => env('SMS_TELEGRAM_CHATS'),
    ],
    'firebase' => [
        'base_url' => env('SMS_BASE_URL'),
        'token' => env('FIREBASE_TOKEN'),
        'telegram_bot_token' => env('SMS_TELEGRAM_BOT_TOKEN'),
        'telegram_chats' => env('SMS_TELEGRAM_CHATS'),
    ],
    'media' => [
        'base_url' => env('MEDIA_BASE_URL'),
        'token' => env('MEDIA_TOKEN'),
    ],
    'crm' => [
        'base_url' => env('CRM_BASE_URL'),
    ],
    'helpdesk' => [
        'base_url' => env('HELPDESK_BASE_URL'),
    ],
];
