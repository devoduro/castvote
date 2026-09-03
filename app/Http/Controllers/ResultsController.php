<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Event;
use App\Models\Nominee;
use Illuminate\Http\Request;

class ResultsController extends Controller
{
    /**
     * Award picker for published standings.
     *
     * Only campaigns whose organiser has switched on "Publish results" appear
     * here, and campaigns configured for an anonymous tally are excluded
     * entirely. Nothing is shown that the organiser has not released.
     */
    public function index(Request $request)
    {
        // Older links used ?award=slug against this route — keep them working.
        if ($slug = $request->query('award')) {
            return redirect()->route('results.show', array_filter([
                'slug'     => $slug,
                'category' => $request->query('category'),
            ]));
        }

        $search = trim((string) $request->query('q', ''));

        $events = $this->publishedEvents()
            ->when($search !== '', fn ($c) => $c->filter(
                fn (Event $e) => str_contains(mb_strtolower($e->name), mb_strtolower($search))
                    || str_contains(mb_strtolower($e->organization?->name ?? ''), mb_strtolower($search))
            ))
            ->values();

        return view('public.results.index', compact('events', 'search'));
    }

    /** Category-by-category rankings for one published campaign. */
    public function show(Request $request, string $slug)
    {
        $event = $this->publishedEvents()->firstWhere('slug', $slug);

        abort_if(! $event, 404);

        $categories = $event->categories()->withCount('nominees')->get();
        $category   = $categories->firstWhere('id', (int) $request->query('category')) ?? $categories->first();

        $standings  = $category ? $this->standingsFor($category) : collect();
        $totalVotes = (int) $standings->sum('votes');

        return view('public.results.show', compact(
            'event', 'categories', 'category', 'standings', 'totalVotes'
        ));
    }

    /**
     * Campaigns the public is allowed to see standings for.
     *
     * @return \Illuminate\Support\Collection<int, Event>
     */
    private function publishedEvents()
    {
        return Event::with('organization')
            ->withCount('categories')
            ->whereIn('status', ['live', 'closed'])
            ->get()
            ->filter(fn (Event $e) => $e->resultsArePublic() && ! $e->isAnonymousTally())
            ->sortBy([['status', 'asc'], ['ends_at', 'asc']])
            ->values();
    }

    /**
     * Nominees in a category ordered by vote total, with each nominee's share
     * of the category vote.
     */
    private function standingsFor(Category $category)
    {
        $tallies = $category->votes()
            ->selectRaw('nominee_id, SUM(quantity) as total')
            ->groupBy('nominee_id')
            ->pluck('total', 'nominee_id');

        $total = max((int) $tallies->sum(), 1);

        return Nominee::where('category_id', $category->id)
            ->orderBy('display_order')
            ->get()
            ->map(function (Nominee $nominee) use ($tallies, $total) {
                $votes = (int) ($tallies[$nominee->id] ?? 0);

                return (object) [
                    'nominee' => $nominee,
                    'votes'   => $votes,
                    'share'   => round($votes / $total * 100, 1),
                ];
            })
            ->sortByDesc('votes')
            ->values()
            ->map(function ($row, $i) {
                $row->rank = $i + 1;
                return $row;
            });
    }
}
