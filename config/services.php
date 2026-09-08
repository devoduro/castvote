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

    'paystack' => [
        'public_key' => env('PAYSTACK_PUBLIC_KEY'),
        'secret'     => env('PAYSTACK_SECRET_KEY'),
        'url'        => env('PAYSTACK_PAYMENT_URL', 'https://api.paystack.co'),
    ],

    'nalo' => [
        // USSD gateway. USER ID is issued by Nalo for this integration and is
        // echoed back on every reply; when set, requests presenting a
        // different one are rejected.
        'user_id'  => env('NALO_USER_ID'),
        'api_key'  => env('NALO_API_KEY'),
        'password' => env('NALO_PASSWORD'),
        // The dialled extension, for reference in the USSD Manager.
        'extension' => env('NALO_EXTENSION', env('USSD_SHORTCODE')),
        'sms_url'  => env('NALO_SMS_URL', 'https://sms.nalosolutions.com/smsbackend/clientapi/Resl_Nalo/send-message/'),
        'sender_id' => env('NALO_SENDER_ID', 'ClickVote'),
    ],

    'speso' => [
        'base_url'       => env('SPESO_BASE_URL', 'https://business.speso.co/api/v1'),
        'api_key'        => env('SPESO_API_KEY'),
        'webhook_secret' => env('SPESO_WEBHOOK_SECRET'),
        'callback_url'   => env('SPESO_CALLBACK_URL'),
        'sender_id'      => env('SPESO_SENDER_ID', 'ClickVote'),
    ],

    'arkesel' => [
        'api_key'   => env('ARKESEL_API_KEY'),
        'sender_id' => env('ARKESEL_SMS_SENDER_ID', 'ClickVote'),
        'ussd_mode' => env('ARKESEL_USSD_MODE', 'text'), // 'text' or 'json'
    ],

];
