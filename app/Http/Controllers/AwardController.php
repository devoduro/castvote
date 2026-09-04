<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Event;
use App\Models\Nominee;
use Illuminate\Http\Request;

class AwardController extends Controller
{
    /** Award shows only — the /awards listing. */
    public function awards(Request $request)
    {
        return $this->index($request, 'award');
    }

    /**
     * The voting entry point: every campaign currently accepting votes, as a
     * searchable picker. Deliberately lighter than the /awards directory —
     * one job, one call to action per card.
     */
    public function voting(Request $request)
    {
        $search = trim((string) $request->query('q', ''));

        $events = Event::query()
            ->with('organization')
            ->withCount('categories')
            ->where('status', 'live')
            ->when($search !== '', fn ($q) => $q->where(function ($w) use ($search) {
                $w->where('name', 'like', "%{$search}%")
                  ->orWhereHas('organization', fn ($o) => $o->where('name', 'like', "%{$search}%"));
            }))
            ->orderBy('ends_at')
            ->get()
            ->filter->isLive()
            ->values();

        $shortcode = Event::where('status', 'live')->whereNotNull('ussd_shortcode')->value('ussd_shortcode')
            ?: config('clickvote.ussd_shortcode');

        return view('public.voting', compact('events', 'search', 'shortcode'));
    }

    /**
     * Directory of public campaigns.
     *
     * @param  string|null  $only  'award' restricts the listing to award shows;
     *                             null lists every public campaign type.
     */
    public function index(Request $request, ?string $only = null)
    {
        $search = trim((string) $request->query('q', ''));
        $type   = $only ?? $request->query('type', 'all');
        $status = $request->query('status', 'all');
        $sort   = $request->query('sort', 'closing');

        $query = Event::query()
            ->with('organization')
            ->withCount('categories')
            ->whereIn('status', ['live', 'closed']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('organization', fn ($o) => $o->where('name', 'like', "%{$search}%"));
            });
        }

        if ($type !== 'all') {
            $query->where('event_type', $type);
        }

        if ($status === 'live') {
            $query->where('status', 'live')->where('ends_at', '>=', now());
        } elseif ($status === 'closed') {
            $query->where(fn ($q) => $q->where('status', 'closed')->orWhere('ends_at', '<', now()));
        }

        match ($sort) {
            'newest'  => $query->latest('starts_at'),
            'name'    => $query->orderBy('name'),
            default   => $query->orderByRaw("CASE WHEN status = 'live' THEN 0 ELSE 1 END")->orderBy('ends_at'),
        };

        $events = $query->paginate(9)->withQueryString();

        // Type facets come from the data, so a new campaign type appears on its own.
        $types = Event::whereIn('status', ['live', 'closed'])
            ->distinct()->orderBy('event_type')->pluck('event_type');

        return view('public.awards.index', [
            'events'  => $events,
            'types'   => $types,
            'search'  => $search,
            'type'    => $type,
            'status'  => $status,
            'sort'    => $sort,
            'locked'  => $only !== null,
            'heading' => $only === 'award' ? 'Awards' : 'Events',
            'intro'   => $only === 'award'
                ? 'Discover ongoing award campaigns and support your favourite nominees.'
                : 'Every voting campaign on ClickVote — award shows, elections and AGMs.',
        ]);
    }

    /** Award landing page: banner, details and category grid. */
    public function show(string $slug)
    {
        $event = Event::with('organization')
            ->whereIn('status', ['live', 'closed'])
            ->where('slug', $slug)
            ->firstOrFail();

        $categories = $event->categories()->withCount('nominees')->get();

        $stats = [
            'categories' => $categories->count(),
            'nominees'   => (int) $categories->sum('nominees_count'),
            'votes'      => $event->resultsArePublic() ? $event->totalVotes() : null,
        ];

        return view('public.awards.show', compact('event', 'categories', 'stats'));
    }

    /** Nominees within one category of an award. */
    public function category(string $slug, Category $category)
    {
        $event = Event::with('organization')
            ->whereIn('status', ['live', 'closed'])
            ->where('slug', $slug)
            ->firstOrFail();

        abort_if($category->event_id !== $event->id, 404);

        $showVotes = $event->resultsArePublic() && ! $event->isAnonymousTally();

        $nominees = Nominee::where('category_id', $category->id)
            ->orderBy('display_order')
            ->get();

        $tallies = $showVotes ? $this->talliesFor($category) : collect();

        return view('public.awards.category', compact('event', 'category', 'nominees', 'showVotes', 'tallies'));
    }

    /** nominee_id => vote total for a category, in a single query. */
    private function talliesFor(Category $category)
    {
        return $category->votes()
            ->selectRaw('nominee_id, SUM(quantity) as total')
            ->groupBy('nominee_id')
            ->pluck('total', 'nominee_id');
    }
}
