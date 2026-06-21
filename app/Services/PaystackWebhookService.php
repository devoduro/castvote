<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Payment;
use App\Models\Vote;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaystackWebhookService
{
    public function __construct(private readonly ArkeselSmsService $sms) {}

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

        // Lock the payment row for update — prevents duplicate webhook races
        return DB::transaction(function () use ($reference, $data) {
            $payment = Payment::where('provider_reference', $reference)
                ->lockForUpdate()
                ->first();

            if (!$payment) {
                // Unknown reference — log and return 200 so Paystack stops retrying
                Log::warning('Paystack charge.success for unknown reference', ['ref' => $reference]);
                return false;
            }

            // Idempotency guard — already credited
            if ($payment->isSuccess()) {
                Log::info('Duplicate charge.success ignored', ['ref' => $reference]);
                return true;
            }

            // Verify amount matches what we expected (guard against partial-payment attacks)
            $paystackAmount = (int) ($data['amount'] ?? 0);
            if ($paystackAmount !== $payment->amount_pesewas) {
                Log::error('Paystack amount mismatch', [
                    'ref'      => $reference,
                    'expected' => $payment->amount_pesewas,
                    'received' => $paystackAmount,
                ]);
                $payment->markFailed($data);
                return false;
            }

            // Mark payment successful
            $payment->markSuccess($data);

            // Credit the vote from metadata stored at charge-initiation time
            $meta       = $payment->metadata ?? [];
            $nomineeId  = $meta['nominee_id']  ?? null;
            $categoryId = $meta['category_id'] ?? null;
            $quantity   = (int) ($meta['quantity'] ?? 1);
            $channel    = $meta['channel']      ?? 'ussd';

            if ($nomineeId && $categoryId) {
                $vote = Vote::create([
                    'event_id'    => $payment->event_id,
                    'category_id' => $categoryId,
                    'nominee_id'  => $nomineeId,
                    'quantity'    => $quantity,
                    'channel'     => $channel,
                    'voter_phone' => $payment->event->isAnonymousTally()
                        ? null
                        : $payment->phone_number,
                    'payment_id'  => $payment->id,
                ]);

                Log::info('Vote credited', [
                    'vote_id'    => $vote->id,
                    'nominee_id' => $nomineeId,
                    'quantity'   => $quantity,
                    'ref'        => $reference,
                ]);
            }

            // Send SMS confirmation (non-blocking — fire and forget)
            $this->dispatchSmsConfirmation($payment, $quantity);

            AuditLog::record(
                action:  'payment.success',
                subject: $payment,
                meta:    ['reference' => $reference, 'amount_pesewas' => $payment->amount_pesewas],
                adminId: null,
            );

            return true;
        });
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

        return DB::transaction(function () use ($reference, $data) {
            $payment = Payment::where('provider_reference', $reference)
                ->lockForUpdate()
                ->first();

            if (!$payment || !$payment->isPending()) {
                return true; // Already resolved — idempotent
            }

            $payment->markFailed($data);

            // Notify voter of failure via SMS
            $this->sms->send(
                phone:   $payment->phone_number,
                message: "Your vote payment of GHS {$payment->amountInGhs()} was unsuccessful. " .
                         "Please dial *928*24# to try again. Ref: {$payment->provider_reference}",
            );

            Log::info('Payment failed', ['ref' => $reference]);
            return true;
        });
    }

    // -------------------------------------------------------------------------
    // SMS confirmation dispatch
    // -------------------------------------------------------------------------

    private function dispatchSmsConfirmation(Payment $payment, int $quantity): void
    {
        $meta      = $payment->metadata ?? [];
        $nominee   = \App\Models\Nominee::find($meta['nominee_id'] ?? null);
        $nomName   = $nominee?->name ?? 'your nominee';
        $eventName = $payment->event->name ?? 'the event';

        $this->sms->send(
            phone:   $payment->phone_number,
            message: "CastVote: Your {$quantity} vote(s) for {$nomName} in {$eventName} " .
                     "have been confirmed! GHS {$payment->amountInGhs()} charged. " .
                     "Ref: {$payment->provider_reference}. Thank you for voting!",
        );
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
