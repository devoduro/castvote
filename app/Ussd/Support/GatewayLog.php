<?php

namespace App\Ussd\Support;

use Illuminate\Support\Facades\Cache;

/**
 * A short, rolling record of what the gateway actually sent us.
 *
 * The USSD endpoint is the one place we cannot inspect from a browser, so
 * when a shortcode misbehaves the first question is always "did the request
 * even arrive, and what was in it?". This keeps the last few inbound requests
 * where the superadmin can see them, without digging through server logs.
 *
 * The MSISDN is stored in full. The whole purpose of the panel is to answer
 * "was that dial mine?", which a masked number cannot do — 024****456 and
 * 024****456 are indistinguishable whether they are one handset or two. The
 * page is superadmin-only, the entries expire after a day, and nothing here is
 * written to disk.
 */
class GatewayLog
{
    private const KEY = 'ussd.gateway_log';

    private const KEEP = 15;

    private const TTL = 86400;   // one day

    /**
     * @param  array<string, mixed>  $data     Lower-cased inbound payload.
     * @param  string               $outcome  What we did with it.
     */
    public static function record(array $data, string $outcome): void
    {
        try {
            $entries = self::recent();

            array_unshift($entries, [
                'at'       => now()->toDateTimeString(),
                'userid'   => (string) ($data['userid'] ?? '—'),
                'msisdn'   => (string) ($data['msisdn'] ?? $data['phonenumber'] ?? ''),
                'normalised' => PhoneNumber::normalize((string) ($data['msisdn'] ?? $data['phonenumber'] ?? '')),
                'userdata' => (string) ($data['userdata'] ?? $data['input'] ?? ''),
                'msgtype'  => $data['msgtype'] ?? null,
                'session'  => (string) ($data['sessionid'] ?? $data['session_id'] ?? '—'),
                'outcome'  => $outcome,
                'keys'     => implode(', ', array_map('strtoupper', array_keys($data))),
            ]);

            Cache::put(self::KEY, array_slice($entries, 0, self::KEEP), self::TTL);
        } catch (\Throwable $e) {
            // Diagnostics must never break a live USSD session.
        }
    }

    /** @return array<int, array<string, mixed>> */
    public static function recent(): array
    {
        try {
            return (array) Cache::get(self::KEY, []);
        } catch (\Throwable $e) {
            return [];
        }
    }

    public static function clear(): void
    {
        try {
            Cache::forget(self::KEY);
        } catch (\Throwable $e) {
            //
        }
    }
}
