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

    'openai' => [
        'key' => env('OPENAI_API_KEY'),
    ],

    'stripe' => [
        'public_key' => env('STRIPE_PUBLIC_KEY'),
        'secret_key' => env('STRIPE_SECRET_KEY'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        'currency' => strtolower((string) env('STRIPE_CURRENCY', 'eur')),
        'currencies' => [
            'en' => strtolower((string) env('STRIPE_CURRENCY_EN', env('STRIPE_CURRENCY', 'eur'))),
            'ro' => strtolower((string) env('STRIPE_CURRENCY_RO', 'ron')),
        ],
        'products' => [
            'rental_living' => env('STRIPE_PRODUCT_RENTAL_LIVING'),
            'buying_living' => env('STRIPE_PRODUCT_BUYING_LIVING'),
        ],
        'prices' => [
            'rental_living' => env('STRIPE_PRICE_RENTAL_LIVING'),
            'buying_living' => env('STRIPE_PRICE_BUYING_LIVING'),
        ],
    ],

    'smartbill' => [
        'username' => env('SMARTBILL_USERNAME'),
        'token' => env('SMARTBILL_TOKEN'),
        'company_vat_code' => env('SMARTBILL_COMPANY_VAT_CODE'),
        'base_url' => rtrim((string) env('SMARTBILL_BASE_URL', 'https://ws.smartbill.ro/SBORO/api'), '/'),
        'timeout' => (int) env('SMARTBILL_TIMEOUT', 20),
        'invoice' => [
            'series' => env('SMARTBILL_INVOICE_SERIES'),
            'payment_type' => env('SMARTBILL_PAYMENT_TYPE', 'Card online'),
            'test_draft' => env('SMARTBILL_TEST_FLOW_DRAFT', true),
            'tax_name' => env('SMARTBILL_TAX_NAME'),
            'tax_percentage' => env('SMARTBILL_TAX_PERCENTAGE'),
            'tax_included' => env('SMARTBILL_TAX_INCLUDED'),
        ],
    ],

    'exchange_rates' => [
        'eur_ron_url' => env(
            'EXCHANGE_RATE_EUR_RON_URL',
            'https://api.frankfurter.app/latest?from=EUR&to=RON',
        ),
        'timeout' => (int) env('EXCHANGE_RATE_TIMEOUT', 10),
    ],

    'google_tag_manager' => [
        'container_id' => env('GOOGLE_TAG_MANAGER_ID', 'GTM-TDKGXXHL'),
    ],

    'browsershot' => [
        'chrome_path' => env('BROWSERSHOT_CHROME_PATH') ?: env('PUPPETEER_EXECUTABLE_PATH'),
        'node_binary' => env('BROWSERSHOT_NODE_BINARY'),
        'npm_binary' => env('BROWSERSHOT_NPM_BINARY'),
        'no_sandbox' => env('BROWSERSHOT_NO_SANDBOX', true),
        'disable_dev_shm_usage' => env('BROWSERSHOT_DISABLE_DEV_SHM_USAGE', true),
        'disable_gpu' => env('BROWSERSHOT_DISABLE_GPU', true),
        'disable_setuid_sandbox' => env('BROWSERSHOT_DISABLE_SETUID_SANDBOX', true),
        'write_options_to_file' => env('BROWSERSHOT_WRITE_OPTIONS_TO_FILE', false),
        'timeout' => env('BROWSERSHOT_TIMEOUT', 180),
    ],

];
