<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Payment;
use App\Models\Vote;
use Illuminate\Support\Collection;

/**
 * Verifies that the votes table is consistent with the payments ledger.
 * Every vote row must have a corresponding successful payment, and the
 * quantities must match. Run this via the artisan command below or from
 * the admin fraud panel.
 */
class VoteIntegrityService
{
    /**
     * Returns any integrity violations found for an event.
     * An empty collection means the ledger is clean.
     */
    public function verify(Event $event): Collection
    {
        $violations = collect();

        // 1. Votes with no payment (orphaned votes)
        $orphaned = Vote::where('event_id', $event->id)
            ->whereNull('payment_id')
            ->get();

        foreach ($orphaned as $vote) {
            $violations->push([
                'type'     => 'orphaned_vote',
                'severity' => 'high',
                'message'  => "Vote #{$vote->id} has no payment_id — possible manual insertion.",
                'vote_id'  => $vote->id,
            ]);
        }

        // 2. Votes whose linked payment is not in 'success' status
        $badPayment = Vote::where('event_id', $event->id)
            ->whereNotNull('payment_id')
            ->whereHas('payment', fn($q) => $q->whereNotIn('status', ['success']))
            ->with('payment')
            ->get();

        foreach ($badPayment as $vote) {
            $violations->push([
                'type'     => 'vote_on_non_success_payment',
                'severity' => 'high',
                'message'  => "Vote #{$vote->id} linked to payment #{$vote->payment_id} with status '{$vote->payment->status}'.",
                'vote_id'  => $vote->id,
            ]);
        }

        // 3. Quantity mismatch between vote and payment metadata
        $mismatch = Vote::where('event_id', $event->id)
            ->whereNotNull('payment_id')
            ->with('payment')
            ->get()
            ->filter(function ($vote) {
                $expected = $vote->payment?->metadata['quantity'] ?? null;
                return $expected !== null && (int) $expected !== (int) $vote->quantity;
            });

        foreach ($mismatch as $vote) {
            $expected = $vote->payment->metadata['quantity'];
            $violations->push([
                'type'     => 'quantity_mismatch',
                'severity' => 'medium',
                'message'  => "Vote #{$vote->id} has quantity {$vote->quantity} but payment metadata says {$expected}.",
                'vote_id'  => $vote->id,
            ]);
        }

        // 4. Duplicate votes for same payment_id (idempotency breach)
        $duplicates = Vote::where('event_id', $event->id)
            ->selectRaw('payment_id, COUNT(*) as cnt')
            ->whereNotNull('payment_id')
            ->groupBy('payment_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $dup) {
            $violations->push([
                'type'     => 'duplicate_vote',
                'severity' => 'critical',
                'message'  => "Payment #{$dup->payment_id} has {$dup->cnt} vote rows — idempotency breach.",
                'payment_id' => $dup->payment_id,
            ]);
        }

        // 5. Successful payments with no vote credited (missed webhook)
        $uncredited = Payment::where('event_id', $event->id)
            ->where('status', 'success')
            ->whereDoesntHave('vote')
            ->where('created_at', '<', now()->subMinutes(10)) // grace period
            ->get();

        foreach ($uncredited as $payment) {
            $violations->push([
                'type'       => 'uncredited_payment',
                'severity'   => 'medium',
                'message'    => "Payment #{$payment->id} (ref: {$payment->provider_reference}) succeeded but no vote row exists.",
                'payment_id' => $payment->id,
            ]);
        }

        return $violations;
    }

    /**
     * Replay the payments ledger to recompute vote totals per nominee.
     * Returns the authoritative tally derived purely from successful payments.
     */
    public function ledgerReplay(Event $event): Collection
    {
        return Payment::where('event_id', $event->id)
            ->where('status', 'success')
            ->whereNotNull('metadata->nominee_id')
            ->get()
            ->groupBy(fn($p) => $p->metadata['nominee_id'])
            ->map(fn($payments, $nomineeId) => [
                'nominee_id'   => $nomineeId,
                'total_votes'  => $payments->sum(fn($p) => $p->metadata['quantity'] ?? 0),
                'total_amount' => $payments->sum('amount_pesewas'),
                'transactions' => $payments->count(),
            ])
            ->sortByDesc('total_votes')
            ->values();
    }
}
