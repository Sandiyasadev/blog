<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Anti-Spam Configuration
    |--------------------------------------------------------------------------
    |
    | These options control the anti-spam features for blog comments.
    | You can disable specific features during development or when traffic
    | is still low and manual moderation is preferred.
    |
    */

    'antispam' => [
        /*
        |--------------------------------------------------------------------------
        | CAPTCHA (Cloudflare Turnstile)
        |--------------------------------------------------------------------------
        |
        | Enable/disable CAPTCHA verification for comment submissions.
        | Even if enabled, CAPTCHA requires valid TURNSTILE_SITE_KEY and
        | TURNSTILE_SECRET_KEY to be configured in services.php.
        |
        */
        'captcha_enabled' => env('ANTISPAM_CAPTCHA_ENABLED', true),

        /*
        |--------------------------------------------------------------------------
        | Rate Limiting
        |--------------------------------------------------------------------------
        |
        | Enable/disable rate limiting for comment submissions.
        | When enabled, limits to 5 comments per minute per IP address.
        |
        */
        'rate_limit_enabled' => env('ANTISPAM_RATE_LIMIT_ENABLED', true),

        /*
        |--------------------------------------------------------------------------
        | Spam Detection
        |--------------------------------------------------------------------------
        |
        | Enable/disable the spam detection algorithm that analyzes
        | comment content, author name, and URLs for spam patterns.
        |
        */
        'spam_detection_enabled' => env('ANTISPAM_SPAM_DETECTION_ENABLED', true),

        /*
        |--------------------------------------------------------------------------
        | Auto-Approve Clean Comments
        |--------------------------------------------------------------------------
        |
        | When spam detection is enabled, automatically approve comments
        | that are detected as clean (spam score = 0).
        | Set to false to require manual moderation for all comments.
        |
        */
        'auto_approve_clean' => env('ANTISPAM_AUTO_APPROVE_CLEAN', false),

        /*
        |--------------------------------------------------------------------------
        | Auto-Approve When Spam Detection Disabled
        |--------------------------------------------------------------------------
        |
        | When spam detection is disabled, automatically approve all comments.
        | Set to false to still require manual moderation.
        |
        */
        'auto_approve_when_disabled' => env('ANTISPAM_AUTO_APPROVE_WHEN_DISABLED', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Configure caching behavior for blog posts and other content.
    |
    */

    'cache' => [
        'enabled' => env('BLOG_CACHE_ENABLED', true),
        'ttl' => env('BLOG_CACHE_TTL', 3600), // 1 hour default
    ],

];
