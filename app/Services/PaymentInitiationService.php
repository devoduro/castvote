<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Payment;
use Illuminate\Support\Str;

/**
 * Shared payment initiation logic used by both the web portal (Paystack Inline)
 * and the USSD flow (Paystack Charge API via queued job).
 * Returns a Payment model in 'pending' status ready for either:
 *  - Web: front-end Paystack Inline popup (public key + reference passed to JS)
 *  - USSD: InitiatePaystackCharge queued job
 */
class PaymentInitiationService
{
    public function createPendingPayment(
        Event  $event,
        string $phone,
        int    $nomineeId,
        int    $categoryId,
        int    $quantity,
        string $channel = 'web',
    ): Payment {
        $amountPesewas = $quantity * $event->pricePerVotePesewas();
        $network       = $this->detectNetwork($phone);

        return Payment::create([
            'event_id'           => $event->id,
            'provider'           => 'paystack',
            'provider_reference' => 'cv_' . Str::random(24),
            'amount_pesewas'     => $amountPesewas,
            'currency'           => 'GHS',
            'phone_number'       => $phone,
            'momo_network'       => $network,
            'status'             => 'pending',
            'metadata'           => [
                'nominee_id'  => $nomineeId,
                'category_id' => $categoryId,
                'quantity'    => $quantity,
                'channel'     => $channel,
            ],
        ]);
    }

    /**
     * Build the config array for Paystack Inline JS popup (web voting portal).
     */
    public function paystackInlineConfig(Payment $payment, string $callbackUrl): array
    {
        return [
            'key'       => config('services.paystack.public_key'),
            'email'     => $payment->phone_number . '@web.castvote.placeholder',
            'amount'    => $payment->amount_pesewas,
            'currency'  => 'GHS',
            'ref'       => $payment->provider_reference,
            'callback'  => $callbackUrl,
            'onClose'   => 'function(){ console.log("Payment popup closed"); }',
            'metadata'  => [
                'phone'      => $payment->phone_number,
                'payment_id' => $payment->id,
            ],
        ];
    }

    private function detectNetwork(string $phone): string
    {
        $local  = preg_replace('/^(\+?233)/', '0', $phone);
        $prefix = substr($local, 0, 3);

        if (in_array($prefix, ['020', '050'])) {
            return 'vodafone';
        }
        if (in_array($prefix, ['026', '056', '027', '057'])) {
            return 'airteltigo';
        }
        return 'mtn';
    }
}
