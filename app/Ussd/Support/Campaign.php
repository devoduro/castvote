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

        $reachable = self::reachableCampaigns();

        if ($reachable->count() === 1) {
            return new self($reachable->first());
        }

        return new self(null, $reachable->isNotEmpty());
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

    /**
     * Campaigns a caller can reach by dialling — open ones first, then those
     * whose voting has closed.
     *
     * Closed campaigns stay on the menu so the shortcode does not go dead the
     * moment voting ends; the ballot is refused further in, by RouteWelcome-
     * Action and again by PlaceVoteAction, so nothing can be voted or charged
     * after the close.
     *
     * The picker screen and the action that reads the caller's choice must
     * both call this, or the numbering they show and the numbering they read
     * would drift apart.
     */
    public static function reachableCampaigns()
    {
        return Event::whereIn('status', ['live', 'closed'])
            ->orderBy('ends_at')
            ->get()
            ->filter(fn (Event $event) => $event->isLive() || $event->votingHasClosed())
            ->sortBy(fn (Event $event) => $event->isLive() ? 0 : 1)
            ->values();
    }
}
