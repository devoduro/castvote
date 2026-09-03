<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Support\Facades\Log;

class PaystackWebhookService
{
    public function __construct(private readonly VoteCreditService $credit) {}

    /**
     * Verify the X-Paystack-Signature header against the raw request body.
     * Must be called BEFORE json_decode so the body is still raw bytes.
     */
    public function verifySignature(string $rawBody, string $signature): bool
    {
        $expected = hash_hmac('sha512', $rawBody, config('services.paystack.secret'));
        return hash_equals($expected, $signature);
    }

    /**
     * Dispatch to the right handler based on Paystack event type.
     * Returns false if the event is not one we act on.
     */
    public function handle(array $payload): bool
    {
        $event = $payload['event'] ?? '';

        return match ($event) {
            'charge.success' => $this->handleChargeSuccess($payload['data'] ?? []),
            'charge.failed'  => $this->handleChargeFailed($payload['data'] ?? []),
            default          => false,
        };
    }

    // -------------------------------------------------------------------------
    // charge.success
    // -------------------------------------------------------------------------

    private function handleChargeSuccess(array $data): bool
    {
        $reference = $data['reference'] ?? null;

        if (!$reference) {
            Log::warning('Paystack charge.success missing reference', compact('data'));
            return false;
        }

        // Settlement rules (row locking, idempotency, amount verification,
        // vote creation, SMS) live in VoteCreditService so Paystack and Speso
        // behave identically.
        return $this->credit->credit($reference, (int) ($data['amount'] ?? 0), $data);
    }

    // -------------------------------------------------------------------------
    // charge.failed
    // -------------------------------------------------------------------------

    private function handleChargeFailed(array $data): bool
    {
        $reference = $data['reference'] ?? null;

        if (!$reference) {
            return false;
        }

        return $this->credit->fail($reference, $data);
    }

    // -------------------------------------------------------------------------
    // Manual re-verify (for stuck pending payments — called from admin portal)
    // -------------------------------------------------------------------------

    public function reverify(Payment $payment): bool
    {
        if (!$payment->isPending()) {
            return false;
        }

        $response = \Illuminate\Support\Facades\Http::withToken(config('services.paystack.secret'))
            ->get(config('services.paystack.url') . '/transaction/verify/' . $payment->provider_reference);

        if ($response->failed()) {
            Log::error('Paystack reverify failed', [
                'payment_id' => $payment->id,
                'status'     => $response->status(),
            ]);
            return false;
        }

        $data   = $response->json('data', []);
        $status = $data['status'] ?? '';

        if ($status === 'success') {
            return $this->handleChargeSuccess($data);
        }

        if ($status === 'failed') {
            return $this->handleChargeFailed($data);
        }

        return false; // Still pending at Paystack
    }
}
