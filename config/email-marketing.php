<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Email Marketing Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration options for the email marketing
    | module including sending limits, tracking settings, and more.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Sending Limits
    |--------------------------------------------------------------------------
    |
    | These options control how many emails can be sent per hour/day to
    | prevent overwhelming email servers and avoid being marked as spam.
    |
    */
    'limits' => [
        'daily_send_limit' => env('EMAIL_DAILY_LIMIT', 1000),
        'hourly_send_limit' => env('EMAIL_HOURLY_LIMIT', 100),
        'batch_size' => env('EMAIL_BATCH_SIZE', 50),
        'delay_between_batches' => env('EMAIL_BATCH_DELAY', 60), // seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Tracking Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for email tracking including open rates, click tracking,
    | and pixel configuration.
    |
    */
    'tracking' => [
        'pixel_enabled' => env('EMAIL_TRACKING_ENABLED', true),
        'click_tracking_enabled' => env('EMAIL_CLICK_TRACKING_ENABLED', true),
        'track_user_agent' => env('EMAIL_TRACK_USER_AGENT', true),
        'track_ip_address' => env('EMAIL_TRACK_IP', true),
        'pixel_cache_duration' => env('EMAIL_PIXEL_CACHE', 3600), // seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | URLs Configuration
    |--------------------------------------------------------------------------
    |
    | Base URLs for tracking, unsubscribe, and other email-related endpoints.
    |
    */
    'urls' => [
        'tracking_base' => env('APP_URL') . '/email/track/',
        'click_base' => env('APP_URL') . '/email/click/',
        'unsubscribe_base' => env('APP_URL') . '/unsubscribe/',
        'preview_base' => env('APP_URL') . '/email-templates/',
    ],

    /*
    |--------------------------------------------------------------------------
    | Domain Configuration
    |--------------------------------------------------------------------------
    |
    | Configure allowed and blocked domains for email sending.
    |
    */
    'domains' => [
        'allowed_domains' => env('EMAIL_ALLOWED_DOMAINS', ''),
        'blocked_domains' => [
            'tempmail.org',
            '10minutemail.com',
            'guerrillamail.com',
            'mailinator.com',
        ],
        'require_mx_record' => env('EMAIL_REQUIRE_MX', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Template Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for email templates including variable processing and validation.
    |
    */
    'templates' => [
        'max_size' => env('EMAIL_TEMPLATE_MAX_SIZE', 1048576), // 1MB in bytes
        'allowed_variables' => [
            'recipient_name',
            'recipient_email',
            'company_name',
            'company_address',
            'company_phone',
            'current_date',
            'unsubscribe_url',
            'lead_first_name',
            'lead_last_name',
            'lead_full_name',
            'lead_status',
            'lead_source',
            'lead_budget_min',
            'lead_budget_max',
            'lead_interests',
            'assigned_agent_name',
            'tracking_token',
        ],
        'required_variables' => [
            'unsubscribe_url',
        ],
        'sanitize_html' => env('EMAIL_SANITIZE_HTML', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Campaign Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for email campaigns including retry logic and failure handling.
    |
    */
    'campaigns' => [
        'max_recipients' => env('EMAIL_MAX_RECIPIENTS', 10000),
        'retry_failed_sends' => env('EMAIL_RETRY_FAILED', true),
        'max_retry_attempts' => env('EMAIL_MAX_RETRIES', 3),
        'retry_delay' => env('EMAIL_RETRY_DELAY', 300), // seconds
        'auto_pause_on_high_bounce_rate' => env('EMAIL_AUTO_PAUSE_HIGH_BOUNCE', true),
        'high_bounce_rate_threshold' => env('EMAIL_HIGH_BOUNCE_THRESHOLD', 10), // percentage
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for email queue processing.
    |
    */
    'queue' => [
        'connection' => env('EMAIL_QUEUE_CONNECTION', 'database'),
        'queue_name' => env('EMAIL_QUEUE_NAME', 'emails'),
        'job_timeout' => env('EMAIL_JOB_TIMEOUT', 300), // seconds
        'job_tries' => env('EMAIL_JOB_TRIES', 3),
    ],

    /*
    |--------------------------------------------------------------------------
    | Analytics Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for email analytics and reporting.
    |
    */
    'analytics' => [
        'retention_days' => env('EMAIL_ANALYTICS_RETENTION', 365),
        'aggregate_daily_stats' => env('EMAIL_AGGREGATE_STATS', true),
        'export_formats' => ['csv', 'excel', 'pdf'],
        'cache_stats_duration' => env('EMAIL_CACHE_STATS', 3600), // seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Configuration
    |--------------------------------------------------------------------------
    |
    | Security settings for email marketing operations.
    |
    */
    'security' => [
        'token_length' => env('EMAIL_TOKEN_LENGTH', 32),
        'token_expiry' => env('EMAIL_TOKEN_EXPIRY', 86400 * 30), // 30 days in seconds
        'require_double_opt_in' => env('EMAIL_DOUBLE_OPT_IN', false),
        'encrypt_personal_data' => env('EMAIL_ENCRYPT_DATA', true),
        'log_all_sends' => env('EMAIL_LOG_SENDS', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Compliance Configuration
    |--------------------------------------------------------------------------
    |
    | Settings to ensure compliance with email marketing regulations.
    |
    */
    'compliance' => [
        'auto_add_unsubscribe_link' => env('EMAIL_AUTO_UNSUBSCRIBE', true),
        'require_physical_address' => env('EMAIL_REQUIRE_ADDRESS', true),
        'honor_unsubscribe_immediately' => env('EMAIL_IMMEDIATE_UNSUBSCRIBE', true),
        'keep_unsubscribe_records' => env('EMAIL_KEEP_UNSUBSCRIBE_RECORDS', true),
        'gdpr_compliant' => env('EMAIL_GDPR_COMPLIANT', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Values
    |--------------------------------------------------------------------------
    |
    | Default values for various email marketing components.
    |
    */
    'defaults' => [
        'from_name' => env('MAIL_FROM_NAME', config('app.name')),
        'from_email' => env('MAIL_FROM_ADDRESS', 'noreply@example.com'),
        'reply_to' => env('EMAIL_REPLY_TO', env('MAIL_FROM_ADDRESS')),
        'bounce_email' => env('EMAIL_BOUNCE_ADDRESS', env('MAIL_FROM_ADDRESS')),
        'company_address' => env('COMPANY_ADDRESS', ''),
        'company_phone' => env('COMPANY_PHONE', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | A/B Testing Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for A/B testing functionality.
    |
    */
    'ab_testing' => [
        'enabled' => env('EMAIL_AB_TESTING_ENABLED', true),
        'min_sample_size' => env('EMAIL_AB_MIN_SAMPLE', 100),
        'confidence_level' => env('EMAIL_AB_CONFIDENCE', 95), // percentage
        'test_duration_hours' => env('EMAIL_AB_DURATION', 24),
        'auto_select_winner' => env('EMAIL_AB_AUTO_SELECT', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for system notifications about email campaigns.
    |
    */
    'notifications' => [
        'campaign_completed' => env('EMAIL_NOTIFY_COMPLETED', true),
        'campaign_failed' => env('EMAIL_NOTIFY_FAILED', true),
        'high_bounce_rate' => env('EMAIL_NOTIFY_HIGH_BOUNCE', true),
        'low_delivery_rate' => env('EMAIL_NOTIFY_LOW_DELIVERY', true),
        'notification_email' => env('EMAIL_NOTIFICATION_ADDRESS', env('MAIL_FROM_ADDRESS')),
    ],
];