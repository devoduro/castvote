<?php

namespace App\Ussd\Support;

use App\Models\Setting;

/**
 * USSD service settings, stored in the `settings` table and edited from the
 * superadmin USSD Manager. Every one falls back to config/env, so the flow
 * works before anything has been configured in the dashboard.
 */
class UssdSettings
{
    public const FORMATS = [
        'nalo'           => 'Nalo — USERID / MSG / MSGTYPE',
        'auto'           => 'Auto — mirror the request shape',
        'speso'          => 'Speso — prompt / end',
        'speso_input'    => 'Speso — input / end',
        'speso_continue' => 'Speso — continue / end',
        'speso_con'      => 'Speso — CON / END',
        'africastalking' => "Africa's Talking — \"CON …\" / \"END …\" text",
    ];

    public static function enabled(): bool
    {
        return (bool) Setting::get('ussd_enabled', true);
    }

    public static function shortcode(): string
    {
        return (string) Setting::get('ussd_shortcode', config('ussd.shortcode', '*928#'));
    }

    /** Session record lifetime, in seconds. */
    public static function sessionTtl(): int
    {
        return max(60, (int) Setting::get('ussd_session_ttl', (int) config('ussd.cache_ttl', 300)));
    }

    public static function offlineMessage(): string
    {
        return (string) Setting::get(
            'ussd_offline_message',
            'USSD voting is temporarily unavailable. Please try again later or vote online.'
        );
    }

    /**
     * Wire format for the reply. 'auto' echoes the request's own shape; the
     * rest force a specific contract — see App\Ussd\Responses\GatewayResponse.
     */
    public static function responseFormat(): string
    {
        $value = (string) Setting::get('ussd_response_format', config('ussd.response_format', 'auto'));

        return array_key_exists($value, self::FORMATS) ? $value : 'auto';
    }

    /**
     * Menu items per page. Nalo caps a message at 120 characters, so a small
     * page keeps every option visible instead of being cut off.
     */
    public static function itemsPerPage(): int
    {
        return max(2, min(8, (int) Setting::get('ussd_items_per_page', 3)));
    }

    /** Log every inbound gateway payload — useful while wiring up a new one. */
    public static function debugLogging(): bool
    {
        return (bool) Setting::get('ussd_debug_logging', true);
    }
}
