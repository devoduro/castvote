<?php

namespace App\Jobs;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InitiatePaystackCharge implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $backoff = 10; // seconds between retries

    public function __construct(private readonly int $paymentId) {}

    public function handle(): void
    {
        $payment = Payment::find($this->paymentId);

        if (!$payment || !$payment->isPending()) {
            // Already processed or gone — nothing to do
            return;
        }

        // Map our network codes to Paystack's provider codes
        $providerMap = [
            'mtn'        => 'mtn',
            'vodafone'   => 'vod',
            'airteltigo' => 'atl',
        ];

        $provider = $providerMap[$payment->momo_network] ?? 'mtn';

        $response = Http::withToken(config('services.paystack.secret'))
            ->timeout(30)
            ->post(config('services.paystack.url') . '/charge', [
                'email'        => $payment->phone_number . '@ussd.castvote.placeholder',
                'amount'       => $payment->amount_pesewas,
                'currency'     => 'GHS',
                'mobile_money' => [
                    'phone'    => $payment->phone_number,
                    'provider' => $provider,
                ],
                'reference'    => $payment->provider_reference,
                'metadata'     => [
                    'payment_id'  => $payment->id,
                    'custom_fields' => [
                        [
                            'display_name' => 'Payment ID',
                            'variable_name' => 'payment_id',
                            'value'         => $payment->id,
                        ],
                    ],
                ],
            ]);

        if ($response->failed()) {
            Log::error('Paystack charge initiation failed', [
                'payment_id' => $payment->id,
                'reference'  => $payment->provider_reference,
                'status'     => $response->status(),
                'body'       => $response->body(),
            ]);

            // Mark failed only on non-retriable errors (4xx)
            if ($response->clientError()) {
                $payment->markFailed($response->json() ?? []);
            }

            $this->fail(new \RuntimeException('Paystack charge failed: ' . $response->body()));
            return;
        }

        $body   = $response->json();
        $status = $body['data']['status'] ?? '';

        // Paystack returns 'pay_offline' or 'pending' for MoMo — both mean
        // the STK push was sent. Vote credit happens only via webhook.
        Log::info('Paystack charge initiated', [
            'payment_id' => $payment->id,
            'reference'  => $payment->provider_reference,
            'status'     => $status,
        ]);
    }
}
