<?php

return [

    /*
    |--------------------------------------------------------------------------
    | State Class Namespace
    |--------------------------------------------------------------------------
    |
    | Root namespace for USSD state classes, used by `php artisan ussd:state`.
    | CastVote keeps the whole USSD surface under app/Ussd.
    |
    */

    'state_namespace' => env('USSD_STATE_NS', 'App\\Ussd\\States'),

    /*
    |--------------------------------------------------------------------------
    | Action Class Namespace
    |--------------------------------------------------------------------------
    |
    | Root namespace for USSD action classes, used by `php artisan ussd:action`.
    |
    */

    'action_namespace' => env('USSD_ACTION_NS', 'App\\Ussd\\Actions'),

    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    |
    | Cache store holding a caller's session Record between requests. Null
    | falls back to the default store. USSD sessions are short lived, so any
    | shared store (database, redis, file) works — but it must be shared
    | across web workers, so `array` will not do in production.
    |
    */

    'cache_store' => env('USSD_STORE'),

    /*
    |--------------------------------------------------------------------------
    | Time to live
    |--------------------------------------------------------------------------
    |
    | Seconds a record value survives. The package defaults this to null,
    | which stores forever — on a database cache store that leaks a row per
    | key per session, so we set a real expiry. Gateways drop a USSD session
    | long before this.
    |
    */

    'cache_ttl' => (int) env('USSD_TTL', 300),

    /*
    |--------------------------------------------------------------------------
    | Default value
    |--------------------------------------------------------------------------
    |
    | Returned when a record key is missing.
    |
    */

    'cache_default' => env('USSD_DEFAULT_VALUE'),

    /*
    |--------------------------------------------------------------------------
    | Gateway response shape
    |--------------------------------------------------------------------------
    |
    | Different gateways expect different "keep the session open" keywords.
    | Supported: speso (prompt/end), speso_input (input/end),
    | speso_continue (continue/end), speso_con (CON/END),
    | africastalking (a "CON "/"END " prefixed string), nalo (native JSON),
    | or auto to mirror whatever shape the request arrived in.
    |
    */

    'response_format' => env('USSD_RESPONSE_FORMAT', 'auto'),

    /*
    |--------------------------------------------------------------------------
    | Fallback shortcode
    |--------------------------------------------------------------------------
    |
    | Used in voter-facing copy when an event has no shortcode of its own.
    |
    */

    'shortcode' => env('USSD_SHORTCODE', env('CASTVOTE_USSD_SHORTCODE', '*928#')),

];
