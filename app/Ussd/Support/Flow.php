<?php

namespace App\Ussd\Support;

use App\Models\Category;
use App\Models\Event;
use App\Models\Nominee;
use Sparors\Ussd\Record;

/**
 * Helpers shared by the voting states and actions: resolving the campaign
 * behind the dialled shortcode, and paging long lists onto a handset screen.
 */
class Flow
{
    /**
     * Every key a session writes. Kept explicit so a reset can clear one
     * caller's session without touching anybody else's.
     */
    public const SESSION_KEYS = [
        // package-internal
        '__init', '__active', 'sessionId', 'phoneNumber', 'network', 'input',
        // ours
        'event_id', 'category_id', 'category_name', 'nominee_id', 'nominee_name',
        'nominee_code', 'quantity', 'amount_pesewas', 'page', 'error', 'final_message',
        'eligible_voter_id', 'eligibility_tries',
    ];

    /**
     * Start a caller's session from scratch.
     *
     * Never use Record::flush() for this: the package implements it as
     * Cache::clear(), which empties the *entire* application cache — every
     * other caller's in-progress session, settings, the gateway log, all of
     * it. One person dialling in would knock every other voter back to the
     * welcome screen. This deletes only this session's own keys.
     */
    public static function resetSession(Record $record): void
    {
        $record->deleteMultiple(self::SESSION_KEYS);
    }

    /**
     * Items shown per USSD page. Nalo caps a message at 120 characters, so
     * the default is deliberately small; tune it from the USSD Manager.
     */
    public static function perPage(): int
    {
        return max(2, min(8, UssdSettings::itemsPerPage()));
    }

    public const NEXT = '99';

    public const PREV = '98';

    public const BACK = '0';

    /**
     * The live campaign behind a dialled shortcode.
     *
     * Accepts the full dial string (*920*134*240#) or a bare short id (240), and
     * matches on events.ussd_short_id, falling back to ussd_shortcode.
     */
    public static function resolveEvent(?string $serviceCode): ?Event
    {
        $raw = trim((string) $serviceCode);

        if ($raw === '') {
            return null;
        }

        $shortId = rtrim($raw, '#');
        $shortId = str_contains($shortId, '*') ? substr(strrchr($shortId, '*'), 1) : $shortId;

        $event = Event::where('status', 'live')
            ->where(fn ($q) => $q->where('ussd_short_id', $shortId)->orWhere('ussd_shortcode', $raw))
            ->first();

        return $event?->isLive() ? $event : null;
    }

    public static function event(Record $record): ?Event
    {
        return Event::find($record->get('event_id'));
    }

    public static function category(Record $record): ?Category
    {
        return Category::find($record->get('category_id'));
    }

    public static function nominee(Record $record): ?Nominee
    {
        return Nominee::find($record->get('nominee_id'));
    }

    /** Votes allowed in one transaction. */
    public static function maxVotes(Event $event): int
    {
        return max(1, min(50, $event->maxVotesPerVoter() ?? 50));
    }

    /**
     * Slice a list for the requested page.
     *
     * @param  array<int, mixed>  $items
     * @return array{items: array<int, mixed>, offset: int, hasPrev: bool, hasNext: bool, pages: int}
     */
    public static function page(array $items, int $page): array
    {
        $perPage = self::perPage();
        $pages   = max(1, (int) ceil(count($items) / $perPage));
        $page    = max(1, min($page, $pages));
        $offset  = ($page - 1) * $perPage;

        return [
            'items'   => array_slice($items, $offset, $perPage),
            'offset'  => $offset,
            'hasPrev' => $page > 1,
            'hasNext' => $page < $pages,
            'pages'   => $pages,
        ];
    }

    /** Trailing "99. Next / 98. Prev / 0. Back" hints for a paged menu. */
    public static function pagerHints(bool $hasPrev, bool $hasNext, string $backLabel = 'Back'): string
    {
        $hints = [];

        if ($hasNext) {
            $hints[] = self::NEXT.'. Next';
        }

        if ($hasPrev) {
            $hints[] = self::PREV.'. Prev';
        }

        $hints[] = self::BACK.'. '.$backLabel;

        return implode("\n", $hints);
    }

    /**
     * Pull and clear a one-shot error message so it shows once, at the top of
     * the next screen, and never sticks around.
     */
    public static function takeError(Record $record): string
    {
        $error = (string) $record->get('error', '');

        if ($error !== '') {
            $record->delete('error');
        }

        return $error === '' ? '' : $error."\n";
    }
}
