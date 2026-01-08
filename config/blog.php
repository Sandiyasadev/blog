<?php

return [

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
