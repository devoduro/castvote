<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Event;
use App\Models\Nominee;
use Illuminate\Http\Request;

class NomineeController extends Controller
{
    /** Searchable directory of every nominee on a public campaign. */
    public function index(Request $request)
    {
        $search     = trim((string) $request->query('q', ''));
        $awardSlug  = $request->query('award', 'all');
        $categoryId = $request->query('category', 'all');

        $query = Nominee::query()
            ->with(['category.event.organization'])
            ->whereHas('category.event', fn ($q) => $q->whereIn('status', ['live', 'closed']));

        if ($search !== '') {
            $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")
                                       ->orWhere('code', 'like', "%{$search}%"));
        }

        if ($awardSlug !== 'all') {
            $query->whereHas('category.event', fn ($q) => $q->where('slug', $awardSlug));
        }

        if ($categoryId !== 'all') {
            $query->where('category_id', $categoryId);
        }

        $nominees = $query->orderBy('name')->paginate(12)->withQueryString();

        $awards = Event::whereIn('status', ['live', 'closed'])->orderBy('name')->get(['id', 'name', 'slug']);

        // Category options narrow to the selected award so the filter stays usable.
        $categories = Category::query()
            ->when($awardSlug !== 'all',
                fn ($q) => $q->whereHas('event', fn ($e) => $e->where('slug', $awardSlug)),
                fn ($q) => $q->whereHas('event', fn ($e) => $e->whereIn('status', ['live', 'closed'])))
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('public.nominees.index', compact(
            'nominees', 'awards', 'categories', 'search', 'awardSlug', 'categoryId'
        ));
    }

    /** Nominee profile plus the voting panel. */
    public function show(Nominee $nominee)
    {
        $nominee->load(['category.event.organization']);

        $category = $nominee->category;
        $event    = $category?->event;

        abort_if(! $event || ! in_array($event->status, ['live', 'closed'], true), 404);

        $showVotes = $event->resultsArePublic() && ! $event->isAnonymousTally();

        $peers = Nominee::where('category_id', $category->id)
            ->whereKeyNot($nominee->getKey())
            ->orderBy('display_order')
            ->limit(4)
            ->get();

        return view('public.nominees.show', compact('nominee', 'category', 'event', 'showVotes', 'peers'));
    }
}
