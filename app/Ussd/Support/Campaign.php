<?php

namespace App\Ussd\Support;

use App\Models\Event;

/**
 * Works out which campaign a caller has dialled into.
 *
 * Nalo's payload carries no service code — a Nalo integration is identified by
 * its USERID, and on a shared shortcode the dialled suffix arrives as the
 * first USERDATA. So resolution is tried in order of confidence:
 *
 *   1. An explicit service code, when the gateway sends one (Speso, AT).
 *   2. The Nalo USERID matched against an event's ussd_short_id.
 *   3. The first USERDATA, for a shared code like *920*104*3#.
 *   4. The only live campaign, when there is exactly one.
 *   5. Otherwise the caller picks from a menu.
 */
class Campaign
{
    public function __construct(
        public readonly ?Event $event = null,
        public readonly bool $needsChoice = false,
    ) {}

    /**
     * @param  array<string, mixed>  $data  Lower-cased request payload.
     */
    public static function resolve(array $data, string $userId, string $firstInput): self
    {
        $serviceCode = (string) ($data['service_code'] ?? $data['servicecode'] ?? $data['shortcode'] ?? '');

        foreach ([$serviceCode, $userId, $firstInput] as $candidate) {
            if (trim((string) $candidate) === '') {
                continue;
            }

            if ($event = Flow::resolveEvent((string) $candidate)) {
                return new self($event);
            }
        }

        $live = self::liveCampaigns();

        if ($live->count() === 1) {
            return new self($live->first());
        }

        return new self(null, $live->isNotEmpty());
    }

    /** Campaigns currently accepting votes, in the order a caller sees them. */
    public static function liveCampaigns()
    {
        return Event::where('status', 'live')
            ->orderBy('ends_at')
            ->get()
            ->filter->isLive()
            ->values();
    }
}
