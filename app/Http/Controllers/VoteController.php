<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Nominee;
use App\Models\Payment;
use App\Models\Vote;
use Illuminate\Http\Request;

class VoteController extends Controller
{
    /**
     * List all live events.
     */
    public function index()
    {
        $events = Event::whereIn('status', ['live', 'closed'])
            ->orderByRaw("CASE WHEN status = 'live' THEN 0 ELSE 1 END")
            ->orderBy('ends_at')
            ->get();

        $liveCount    = $events->where('status', 'live')->count();
        $totalEvents  = Event::whereIn('status', ['live', 'closed'])->count();
        $totalVotes   = Vote::count();

        return view('vote.index', compact('events', 'liveCount', 'totalEvents', 'totalVotes'));
    }

    /**
     * Show the ballot for a specific event.
     */
    public function event(string $slug)
    {
        $event = Event::where('slug', $slug)
            ->where('status', 'live')
            ->firstOrFail();

        // Check voting window
        if (!$event->isLive()) {
            return redirect()->route('vote.index')
                ->with('error', "Voting for \"{$event->name}\" is not currently open.");
        }

        return view('vote.event', compact('event'));
    }

    /**
     * Paystack Inline callback — runs in the browser after popup closes.
     * We redirect to the confirmed page; the actual vote credit happens
     * only when Paystack's webhook fires (in PaystackWebhookController).
     */
    public function paymentCallback(Request $request)
    {
        $reference = $request->query('ref') ?? $request->query('reference');

        $payment = $reference
            ? Payment::where('provider_reference', $reference)->first()
            : null;

        // Flash context for the confirmed page
        if ($payment && $payment->metadata) {
            $meta    = $payment->metadata;
            $nominee = Nominee::find($meta['nominee_id'] ?? null);

            session([
                'nominee_name'  => $nominee?->name,
                'category_name' => $nominee?->category?->name,
                'quantity'      => $meta['quantity'] ?? 1,
                'amount_ghs'    => number_format($payment->amount_pesewas / 100, 2),
                'reference'     => $reference,
                'event_slug'    => $payment->event?->slug,
            ]);
        }

        return redirect()->route('vote.confirmed');
    }

    /**
     * Confirmation page — shown after Paystack popup closes.
     */
    public function confirmed()
    {
        return view('vote.confirmed');
    }

    /**
     * Privacy policy.
     */
    public function privacy()
    {
        return view('vote.privacy');
    }
}
