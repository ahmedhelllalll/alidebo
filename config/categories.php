<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Categories V2 Feature Flag
    |--------------------------------------------------------------------------
    |
    | When set to false, all public directory queries and business profiles
    | read from the legacy categories table (category_id).
    | When set to true, reads switch to the new 203-category hierarchy
    | (new_category_id). Instant rollback by flipping to false.
    |
    */
    'v2_enabled' => env('CATEGORIES_V2', false),

    /*
    |--------------------------------------------------------------------------
    | Category Redirect HTTP Status
    |--------------------------------------------------------------------------
    |
    | HTTP status code for legacy category redirects (default: 302 during the
    | 48-hour monitoring window). Switch to 301 for permanent SEO redirects
    | after the monitoring window confirms stability and parity.
    |
    */
    'redirect_status' => env('CATEGORIES_REDIRECT_STATUS', 302),
];
