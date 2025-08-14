<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Email Marketing Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options for the email marketing system.
    |
    */

    'enabled' => env('EMAIL_MARKETING_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | SMTP Provider Configuration
    |--------------------------------------------------------------------------
    |
    | Configure your preferred SMTP provider. Supported providers:
    | smtp, sendgrid, mailgun, ses, postmark, mailtrap
    |
    */

    'provider' => env('EMAIL_MARKETING_PROVIDER', 'smtp'),

    'providers' => [
        'smtp' => [
            'host' => env('EMAIL_MARKETING_SMTP_HOST', env('MAIL_HOST')),
            'port' => env('EMAIL_MARKETING_SMTP_PORT', env('MAIL_PORT')),
            'username' => env('EMAIL_MARKETING_SMTP_USERNAME', env('MAIL_USERNAME')),
            'password' => env('EMAIL_MARKETING_SMTP_PASSWORD', env('MAIL_PASSWORD')),
            'encryption' => env('EMAIL_MARKETING_SMTP_ENCRYPTION', 'tls'),
            'timeout' => env('EMAIL_MARKETING_SMTP_TIMEOUT', 60),
        ],

        'sendgrid' => [
            'api_key' => env('SENDGRID_API_KEY'),
            'webhook_verify' => env('SENDGRID_WEBHOOK_VERIFY', true),
            'webhook_signature' => env('SENDGRID_WEBHOOK_SIGNATURE'),
        ],

        'mailgun' => [
            'domain' => env('MAILGUN_DOMAIN'),
            'secret' => env('MAILGUN_SECRET'),
            'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
            'webhook_signing_key' => env('MAILGUN_WEBHOOK_SIGNING_KEY'),
        ],

        'ses' => [
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'configuration_set' => env('SES_CONFIGURATION_SET'),
        ],

        'postmark' => [
            'token' => env('POSTMARK_TOKEN'),
            'message_stream_id' => env('POSTMARK_MESSAGE_STREAM_ID', 'outbound'),
        ],

        'mailtrap' => [
            'username' => env('MAILTRAP_USERNAME'),
            'password' => env('MAILTRAP_PASSWORD'),
            'host' => env('MAILTRAP_HOST', 'smtp.mailtrap.io'),
            'port' => env('MAILTRAP_PORT', 2525),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Configure rate limits to avoid being marked as spam.
    |
    */

    'rate_limiting' => [
        'enabled' => env('EMAIL_MARKETING_RATE_LIMITING', true),
        'emails_per_minute' => env('EMAIL_MARKETING_EMAILS_PER_MINUTE', 10),
        'emails_per_hour' => env('EMAIL_MARKETING_EMAILS_PER_HOUR', 500),
        'emails_per_day' => env('EMAIL_MARKETING_EMAILS_PER_DAY', 5000),
        'delay_between_emails' => env('EMAIL_MARKETING_DELAY_SECONDS', 1), // seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Bounce Handling
    |--------------------------------------------------------------------------
    |
    | Configuration for handling bounced emails.
    |
    */

    'bounce_handling' => [
        'enabled' => env('EMAIL_MARKETING_BOUNCE_HANDLING', true),
        'max_soft_bounces' => env('EMAIL_MARKETING_MAX_SOFT_BOUNCES', 3),
        'max_hard_bounces' => env('EMAIL_MARKETING_MAX_HARD_BOUNCES', 1),
        'auto_unsubscribe_on_hard_bounce' => env('EMAIL_MARKETING_AUTO_UNSUBSCRIBE_HARD_BOUNCE', true),
        'auto_unsubscribe_on_spam' => env('EMAIL_MARKETING_AUTO_UNSUBSCRIBE_SPAM', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Tracking Configuration
    |--------------------------------------------------------------------------
    |
    | Configure email tracking features.
    |
    */

    'tracking' => [
        'enabled' => env('EMAIL_MARKETING_TRACKING_ENABLED', true),
        'open_tracking' => env('EMAIL_MARKETING_OPEN_TRACKING', true),
        'click_tracking' => env('EMAIL_MARKETING_CLICK_TRACKING', true),
        'pixel_transparent' => true,
        'track_unsubscribes' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Settings
    |--------------------------------------------------------------------------
    |
    | Default values for email campaigns.
    |
    */

    'defaults' => [
        'from_email' => env('EMAIL_MARKETING_FROM_EMAIL', env('MAIL_FROM_ADDRESS')),
        'from_name' => env('EMAIL_MARKETING_FROM_NAME', env('MAIL_FROM_NAME')),
        'reply_to' => env('EMAIL_MARKETING_REPLY_TO'),
        'timezone' => env('EMAIL_MARKETING_TIMEZONE', 'America/Mexico_City'),
        'template_variables' => [
            'company_name' => env('APP_NAME', 'InmoApp'),
            'company_address' => env('COMPANY_ADDRESS', ''),
            'company_phone' => env('COMPANY_PHONE', ''),
            'company_website' => env('APP_URL', 'http://localhost'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Content Security
    |--------------------------------------------------------------------------
    |
    | Security settings for email content.
    |
    */

    'security' => [
        'strip_scripts' => true,
        'allowed_tags' => [
            'a', 'b', 'strong', 'i', 'em', 'u', 'span', 'div', 'p', 'br', 'hr',
            'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'ul', 'ol', 'li', 'table',
            'thead', 'tbody', 'tr', 'td', 'th', 'img', 'style'
        ],
        'max_template_size' => 500000, // 500KB
        'max_subject_length' => 255,
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue Configuration
    |--------------------------------------------------------------------------
    |
    | Configure queue settings for email processing.
    |
    */

    'queue' => [
        'connection' => env('EMAIL_MARKETING_QUEUE_CONNECTION', env('QUEUE_CONNECTION', 'database')),
        'name' => env('EMAIL_MARKETING_QUEUE_NAME', 'email-marketing'),
        'batch_size' => env('EMAIL_MARKETING_BATCH_SIZE', 100),
        'retry_after' => env('EMAIL_MARKETING_RETRY_AFTER', 3600), // 1 hour
        'max_attempts' => env('EMAIL_MARKETING_MAX_ATTEMPTS', 3),
    ],

    /*
    |--------------------------------------------------------------------------
    | Testing Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for testing emails.
    |
    */

    'testing' => [
        'enabled' => env('EMAIL_MARKETING_TESTING', false),
        'test_emails' => env('EMAIL_MARKETING_TEST_EMAILS', ''),
        'override_recipients' => env('EMAIL_MARKETING_OVERRIDE_RECIPIENTS', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Compliance
    |--------------------------------------------------------------------------
    |
    | GDPR and CAN-SPAM compliance settings.
    |
    */

    'compliance' => [
        'require_double_opt_in' => env('EMAIL_MARKETING_DOUBLE_OPT_IN', false),
        'include_physical_address' => env('EMAIL_MARKETING_INCLUDE_PHYSICAL_ADDRESS', true),
        'unsubscribe_footer' => true,
        'list_unsubscribe_header' => true,
        'privacy_policy_url' => env('PRIVACY_POLICY_URL', ''),
        'terms_of_service_url' => env('TERMS_OF_SERVICE_URL', ''),
    ],
];