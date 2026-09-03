<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Nominee;
use App\Models\Vote;

class HomeController extends Controller
{
    /**
     * Public landing page. Every figure shown here is read from the database —
     * nothing on this page is hard-coded.
     */
    public function index()
    {
        $liveEvents = Event::with('organization')
            ->withCount('categories')
            ->where('status', 'live')
            ->orderBy('ends_at')
            ->limit(6)
            ->get()
            ->filter->isLive()
            ->values();

        // Fill the strip with recently closed campaigns when nothing is live.
        $recentEvents = $liveEvents->isEmpty()
            ? Event::with('organization')->withCount('categories')
                ->whereIn('status', ['live', 'closed'])
                ->latest('ends_at')->limit(3)->get()
            : collect();

        $stats = [
            'votes'     => (int) Vote::sum('quantity'),
            'events'    => Event::whereIn('status', ['live', 'closed'])->count(),
            'nominees'  => Nominee::count(),
            'live'      => Event::where('status', 'live')->get()->filter->isLive()->count(),
        ];

        // Nominees are only spotlighted from campaigns whose organiser has
        // published results; otherwise the ordering would leak standings.
        $spotlight = Nominee::query()
            ->with(['category.event.organization'])
            ->whereHas('category.event', fn ($q) => $q->where('status', 'live'))
            ->inRandomOrder()
            ->limit(8)
            ->get();

        return view('public.home', compact('liveEvents', 'recentEvents', 'stats', 'spotlight'));
    }

    public function about()
    {
        $stats = [
            'votes'    => (int) Vote::sum('quantity'),
            'events'   => Event::whereIn('status', ['live', 'closed'])->count(),
            'nominees' => Nominee::count(),
        ];

        return view('public.about', compact('stats'));
    }
}
