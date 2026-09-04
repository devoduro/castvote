<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Public brand & contact details
    |--------------------------------------------------------------------------
    | Surfaced in the site footer, the About page and receipts. Anything left
    | blank is simply not rendered — the UI never invents a channel.
    */

    'tagline' => 'Awards Voting, Nominations & Events Made Simple',

    'contact' => [
        'email'   => env('CLICKVOTE_CONTACT_EMAIL', ''),
        'phone'   => env('CLICKVOTE_CONTACT_PHONE', ''),
        'city'    => env('CLICKVOTE_CONTACT_CITY', 'Accra, Ghana'),
        'hours'   => env('CLICKVOTE_CONTACT_HOURS', 'Mon – Fri, 8:00am – 5:00pm GMT'),
    ],

    /*
    | Default USSD shortcode shown when an event has not been given its own.
    */
    'ussd_shortcode' => env('CLICKVOTE_USSD_SHORTCODE', '*928#'),

    /*
    | Social profiles. Add a URL to make the icon appear in the footer; leave a
    | value empty and that network is hidden. No placeholder links are rendered.
    */
    'social' => array_filter([
        'facebook'  => env('CLICKVOTE_SOCIAL_FACEBOOK', ''),
        'instagram' => env('CLICKVOTE_SOCIAL_INSTAGRAM', ''),
        'tiktok'    => env('CLICKVOTE_SOCIAL_TIKTOK', ''),
        'x'         => env('CLICKVOTE_SOCIAL_X', ''),
    ]),

    /*
    | Platform commission applied to organiser earnings, expressed as a
    | fraction. Mirrors the 5% already used by the earnings dashboard.
    */
    'platform_fee' => 0.05,
];
