<?php

namespace App\Support;

use App\Models\Event;
use App\Models\Nominee;
use App\Models\Organization;
use App\Models\Vote;
use Illuminate\Support\Facades\Cache;
use Throwable;

/**
 * Real platform figures for the marketing panel on the sign-in page.
 *
 * Two rules govern what may appear here, because the page is public and
 * unauthenticated:
 *
 *  1. Nothing invented. Every number is a query.
 *  2. Nothing an organiser considers private. Per-campaign tallies and revenue
 *     are deliberately absent — a campaign's vote counts stay hidden until its
 *     organiser publishes them (Event::resultsArePublic), and a login page must
 *     not be the hole in that promise. Only figures already visible on the
 *     public award pages are used.
 *
 * Results are cached briefly so an unauthenticated page cannot be used to hammer
 * the database, and every accessor degrades to null rather than breaking sign-in.
 */
class PlatformStats
{
    private const TTL = 300;   // five minutes

    /**
     * Headline counters, or an empty array if they cannot be read.
     *
     * @return array<int, array{value: string, label: string}>
     */
    public static function counters(): array
    {
        return self::remember('platform.stats.counters', function () {
            $organisers = Organization::count();
            $votes      = (int) Vote::sum('quantity');
            $campaigns  = Event::whereIn('status', ['live', 'closed'])->count();

            // A brand new deployment has nothing worth boasting about, and
            // "0 Organisers" is a worse first impression than no panel at all.
            if ($organisers === 0 && $votes === 0) {
                return [];
            }

            return [
                ['value' => self::compact($organisers), 'label' => 'Organisers'],
                ['value' => self::compact($votes),      'label' => 'Votes cast'],
                ['value' => self::compact($campaigns),  'label' => 'Campaigns'],
            ];
        }) ?? [];
    }

    /**
     * The campaign to feature: an open one, else the most recently closed.
     *
     * @return array<string, mixed>|null
     */
    public static function featuredCampaign(): ?array
    {
        return self::remember('platform.stats.featured', function () {
            $event = Event::with('organization')
                ->whereIn('status', ['live', 'closed'])
                ->orderByRaw("CASE WHEN status = 'live' THEN 0 ELSE 1 END")
                ->orderByDesc('ends_at')
                ->first();

            if (! $event) {
                return null;
            }

            $categoryIds = $event->categories()->pluck('id');

            return [
                'name'       => $event->name,
                'organiser'  => $event->organization?->name,
                'categories' => $categoryIds->count(),
                'nominees'   => Nominee::whereIn('category_id', $categoryIds)->count(),
                'price'      => $event->priceInGhs(),
                'live'       => $event->isLive(),
                'status'     => $event->statusLabel(),
                'progress'   => self::progress($event),
            ];
        });
    }

    /** How far through its voting window a campaign is, as a percentage. */
    private static function progress(Event $event): int
    {
        if (! $event->starts_at || ! $event->ends_at) {
            return 0;
        }

        if (! $event->isLive()) {
            return $event->votingHasClosed() ? 100 : 0;
        }

        $total = $event->starts_at->diffInSeconds($event->ends_at);

        if ($total <= 0) {
            return 100;
        }

        $elapsed = $event->starts_at->diffInSeconds(now());

        return (int) max(0, min(100, round($elapsed / $total * 100)));
    }

    /** 940 -> "940", 12_400 -> "12.4K", 2_000_000 -> "2M". */
    private static function compact(int $value): string
    {
        if ($value < 1000) {
            return (string) $value;
        }

        foreach ([1_000_000_000 => 'B', 1_000_000 => 'M', 1000 => 'K'] as $unit => $suffix) {
            if ($value < $unit) {
                continue;
            }

            $scaled = $value / $unit;

            return ($scaled < 10 ? rtrim(rtrim(number_format($scaled, 1), '0'), '.') : (string) (int) $scaled).$suffix;
        }

        return (string) $value;
    }

    /** A cache read that never takes the sign-in page down with it. */
    private static function remember(string $key, callable $callback): mixed
    {
        try {
            return Cache::remember($key, self::TTL, $callback);
        } catch (Throwable $e) {
            report($e);

            return null;
        }
    }
}
