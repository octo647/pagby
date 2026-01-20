<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'pagby' => [
        'access_token' => env('PAGBY_ACCESS_TOKEN'),
        'public_key' => env('PAGBY_PUBLIC_KEY'),
        'environment' => env('PAGBY_ENVIRONMENT'),
        'success_url' => env('PAGBY_SUCCESS_URL'),
        'failure_url' => env('PAGBY_FAILURE_URL'),
        'pending_url' => env('PAGBY_PENDING_URL'),
        'webhook_url' => env('PAGBY_WEBHOOK_URL'),
    ],

    'tenant' => [
        'access_token' => env('TENANT_ACCESS_TOKEN'),
        'public_key' => env('TENANT_PUBLIC_KEY'),
        'environment' => env('TENANT_ENVIRONMENT'),
        'success_url' => env('TENANT_SUCCESS_URL'),
        'failure_url' => env('TENANT_FAILURE_URL'),
        'pending_url' => env('TENANT_PENDING_URL'),
        'webhook_url' => env('TENANT_WEBHOOK_URL'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],
    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect' => env('FACEBOOK_REDIRECT_URI'),
    ],

    'asaas' => [
        'api_url' => env('ASAAS_API_URL', 'https://www.asaas.com/api/v3'),
        'api_key' => env('ASAAS_API_KEY_PRODUCTION'),
    ],
];
