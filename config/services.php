<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'adsense' => [
        'client_id' => env('GOOGLE_ADSENSE_CLIENT_ID', env('VITE_GOOGLE_ADSENSE_ID')),
    ],

    'affiliates' => [
        'shopee' => [
            'app_id' => env('SHOPEE_AFFILIATE_APP_ID'),
            'secret' => env('SHOPEE_AFFILIATE_SECRET'),
            'pid' => env('SHOPEE_AFFILIATE_PID', 'an_18380670344'),
        ],
        'mercadolivre' => [
            'app_id' => env('MERCADOLIVRE_AFFILIATE_APP_ID'),
            'secret' => env('MERCADOLIVRE_AFFILIATE_SECRET'),
            'tag' => env('MERCADOLIVRE_AFFILIATE_TAG', '45253537'),
            'word' => env('MERCADOLIVRE_AFFILIATE_WORD', 'thiagomtman'),
        ],
        'amazon' => [
            'tag' => env('AMAZON_AFFILIATE_TAG'),
        ],
    ],

];
