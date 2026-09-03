<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Nominee;
use App\Models\Payment;
use App\Models\Vote;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Provider-agnostic settlement of a pending Payment into a Vote.
 *
 * Both the Paystack and the Speso webhooks funnel through here so the rules
 * that matter — row locking, idempotency, amount verification, anonymous
 * tally handling and SMS confirmation — exist in exactly one place.
 */
class VoteCreditService
{
    public function __construct(private readonly ArkeselSmsService $sms) {}

    /**
     * Mark a payment successful and credit the vote it paid for.
     *
     * @param  string  $reference       Our provider_reference.
     * @param  int     $amountPesewas   Amount the provider says was collected.
     * @param  array   $raw             Raw provider payload, stored for audit.
     */
    public function credit(string $reference, int $amountPesewas, array $raw = []): bool
    {
        return DB::transaction(function () use ($reference, $amountPesewas, $raw) {
            $payment = Payment::where('provider_reference', $reference)
                ->lockForUpdate()
                ->first();

            if (! $payment) {
                Log::warning('Vote credit for unknown reference', ['ref' => $reference]);

                return false;
            }

            // Idempotency guard — a retried webhook must not double-credit.
            if ($payment->isSuccess()) {
                Log::info('Duplicate settlement ignored', ['ref' => $reference]);

                return true;
            }

            // Guard against partial-payment attacks.
            if ($amountPesewas !== $payment->amount_pesewas) {
                Log::error('Settlement amount mismatch', [
                    'ref'      => $reference,
                    'expected' => $payment->amount_pesewas,
                    'received' => $amountPesewas,
                ]);

                $payment->markFailed($raw);

                return false;
            }

            $payment->markSuccess($raw);

            $meta       = $payment->metadata ?? [];
            $nomineeId  = $meta['nominee_id'] ?? null;
            $categoryId = $meta['category_id'] ?? null;
            $quantity   = (int) ($meta['quantity'] ?? 1);
            $channel    = $meta['channel'] ?? 'ussd';

            if ($nomineeId && $categoryId) {
                $vote = Vote::create([
                    'event_id'    => $payment->event_id,
                    'category_id' => $categoryId,
                    'nominee_id'  => $nomineeId,
                    'quantity'    => $quantity,
                    'channel'     => $channel,
                    'voter_phone' => $payment->event?->isAnonymousTally()
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

            $this->sendConfirmation($payment, $quantity);

            AuditLog::record(
                action:  'payment.success',
                subject: $payment,
                meta:    ['reference' => $reference, 'amount_pesewas' => $payment->amount_pesewas],
                adminId: null,
            );

            return true;
        });
    }

    /** Mark a pending payment failed and tell the voter. */
    public function fail(string $reference, array $raw = []): bool
    {
        return DB::transaction(function () use ($reference, $raw) {
            $payment = Payment::where('provider_reference', $reference)
                ->lockForUpdate()
                ->first();

            if (! $payment || ! $payment->isPending()) {
                return true; // Unknown or already resolved — idempotent.
            }

            $payment->markFailed($raw);

            $shortcode = $payment->event?->ussd_shortcode ?: config('ussd.shortcode');

            $this->sms->send(
                phone:   $payment->phone_number,
                message: "Your vote payment of GHS {$payment->amountInGhs()} was unsuccessful. "
                    ."Please dial {$shortcode} to try again. Ref: {$payment->provider_reference}",
            );

            Log::info('Payment failed', ['ref' => $reference]);

            return true;
        });
    }

    private function sendConfirmation(Payment $payment, int $quantity): void
    {
        $meta      = $payment->metadata ?? [];
        $nominee   = Nominee::find($meta['nominee_id'] ?? null);
        $nomName   = $nominee?->name ?? 'your nominee';
        $eventName = $payment->event->name ?? 'the event';

        $this->sms->send(
            phone:   $payment->phone_number,
            message: "CastVote: Your {$quantity} vote(s) for {$nomName} in {$eventName} "
                ."have been confirmed! GHS {$payment->amountInGhs()} charged. "
                ."Ref: {$payment->provider_reference}. Thank you for voting!",
        );
    }
}
