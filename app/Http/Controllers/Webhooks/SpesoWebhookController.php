<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\EligibleVoter;
use App\Models\Payment;
use App\Services\VoteCreditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Speso collection callback.
 *
 * Speso posts the outcome of a Mobile Money collection here. The signature is
 * verified before anything is read, then settlement is handed to
 * VoteCreditService — the same path the Paystack webhook uses — so locking,
 * idempotency and amount checks behave identically on both providers.
 */
class SpesoWebhookController extends Controller
{
    public function __invoke(Request $request, VoteCreditService $credit)
    {
        if (! $this->hasValidSignature($request)) {
            Log::warning('Speso webhook received with an invalid or missing signature');

            return response()->json(['success' => false, 'message' => 'Invalid signature'], 401);
        }

        $validated = $request->validate([
            'order_id'        => ['required', 'string'],
            'speso_reference' => ['required', 'string'],
            'status'          => ['required', 'string', 'in:processing,completed,failed'],
            'amount'          => ['required', 'numeric'],
        ]);

        $payment = Payment::where('provider_reference', $validated['order_id'])->first();

        if (! $payment) {
            Log::warning('Speso webhook for unknown order', ['order_id' => $validated['order_id']]);

            return response()->json(['success' => false, 'message' => 'Order not found'], 404);
        }

        // Speso quotes the amount in cedis; payments are stored in pesewas.
        $amountPesewas = (int) round(((float) $validated['amount']) * 100);

        $raw = $request->all() + ['speso_reference' => $validated['speso_reference']];

        match ($validated['status']) {
            'completed' => $this->settle($payment, $amountPesewas, $raw, $credit),
            'failed'    => $credit->fail($payment->provider_reference, $raw),
            default     => null, // 'processing' — nothing to do until it resolves.
        };

        return response()->json(['success' => true]);
    }

    private function settle(Payment $payment, int $amountPesewas, array $raw, VoteCreditService $credit): void
    {
        $wasCredited = $credit->credit($payment->provider_reference, $amountPesewas, $raw);

        // A USSD ballot on a restricted campaign carries the eligible voter it
        // was issued against; burn it once the vote actually lands.
        if ($wasCredited && ($voterId = $payment->metadata['eligible_voter_id'] ?? null)) {
            EligibleVoter::find($voterId)?->markVoted();
        }
    }

    /**
     * Verifies `X-Speso-Signature: t=<timestamp>,v1=<hmac>` against
     * HMAC-SHA256(t + "." + raw body, webhook secret), rejecting stale
     * (>5 min) or forged callbacks.
     */
    private function hasValidSignature(Request $request): bool
    {
        $secret = config('services.speso.webhook_secret');

        if (! $secret) {
            Log::error('SPESO_WEBHOOK_SECRET is not configured; rejecting webhook.');

            return false;
        }

        $header = (string) $request->header('X-Speso-Signature');

        if (! preg_match('/^t=(\d+),v1=([0-9a-f]+)$/', $header, $matches)) {
            return false;
        }

        [, $timestamp, $signature] = $matches;

        if (abs(time() - (int) $timestamp) > 300) {
            return false;
        }

        $expected = hash_hmac('sha256', $timestamp.'.'.$request->getContent(), $secret);

        return hash_equals($expected, $signature);
    }
}
