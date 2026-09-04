<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Nominee;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class VoteController extends Controller
{
    /**
     * Legacy entry point for the voting portal. The campaign directory now
     * lives at /events (with /awards as the award-show subset), so this keeps
     * the old URL and route name working.
     */
    public function index()
    {
        return redirect()->route('events.index');
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
    public function confirmed(Request $request)
    {
        $reference = $request->query('ref')
            ?? $request->query('reference')
            ?? session('reference');

        $payment = $reference
            ? Payment::with('event')->where('provider_reference', $reference)->first()
            : null;

        return view('vote.confirmed', [
            'payment' => $payment,
            'nominee' => $payment ? Nominee::with('category')->find($payment->metadata['nominee_id'] ?? null) : null,
        ]);
    }

    /**
     * Printable receipt for a single payment. The Paystack reference is a
     * 24-character random string, so the URL acts as the capability to view it.
     * Only non-sensitive fields are rendered.
     */
    public function receipt(string $reference)
    {
        $payment = Payment::with('event.organization')
            ->where('provider_reference', $reference)
            ->firstOrFail();

        $nominee = Nominee::with('category')->find($payment->metadata['nominee_id'] ?? null);

        $pdf = Pdf::loadView('vote.receipt', [
            'payment' => $payment,
            'nominee' => $nominee,
        ])->setPaper('a5');

        return $pdf->download('clickvote-receipt-' . $reference . '.pdf');
    }

    /**
     * Privacy policy.
     */
    public function privacy()
    {
        return view('vote.privacy');
    }
}
