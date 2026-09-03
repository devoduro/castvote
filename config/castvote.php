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
        'email'   => env('CASTVOTE_CONTACT_EMAIL', ''),
        'phone'   => env('CASTVOTE_CONTACT_PHONE', ''),
        'city'    => env('CASTVOTE_CONTACT_CITY', 'Accra, Ghana'),
        'hours'   => env('CASTVOTE_CONTACT_HOURS', 'Mon – Fri, 8:00am – 5:00pm GMT'),
    ],

    /*
    | Default USSD shortcode shown when an event has not been given its own.
    */
    'ussd_shortcode' => env('CASTVOTE_USSD_SHORTCODE', '*928#'),

    /*
    | Social profiles. Add a URL to make the icon appear in the footer; leave a
    | value empty and that network is hidden. No placeholder links are rendered.
    */
    'social' => array_filter([
        'facebook'  => env('CASTVOTE_SOCIAL_FACEBOOK', ''),
        'instagram' => env('CASTVOTE_SOCIAL_INSTAGRAM', ''),
        'tiktok'    => env('CASTVOTE_SOCIAL_TIKTOK', ''),
        'x'         => env('CASTVOTE_SOCIAL_X', ''),
    ]),

    /*
    | Platform commission applied to organiser earnings, expressed as a
    | fraction. Mirrors the 5% already used by the earnings dashboard.
    */
    'platform_fee' => 0.05,
];
