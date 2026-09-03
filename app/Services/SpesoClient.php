<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Thin wrapper over the Speso business API (business.speso.co).
 *
 * Used by the USSD flow to raise a Mobile Money collection prompt on the
 * caller's handset, and to send the SMS confirmation once the vote lands.
 */
class SpesoClient
{
    public function __construct(
        private readonly string $baseUrl,
        private readonly ?string $apiKey,
    ) {}

    public static function make(): self
    {
        return new self(
            rtrim((string) config('services.speso.base_url'), '/'),
            config('services.speso.api_key'),
        );
    }

    public function isConfigured(): bool
    {
        return filled($this->apiKey);
    }

    /**
     * Ask Speso to prompt a customer to approve a Mobile Money payment.
     *
     * @param  string  $orderId  Our own reference — echoed back on the webhook.
     * @param  float   $amount   Major units (GHS), not pesewas.
     */
    public function requestCollection(
        string $orderId,
        string $mobile,
        string $network,
        float $amount,
        ?string $description = null,
        ?string $callbackUrl = null,
    ): array {
        $callbackUrl ??= config('services.speso.callback_url');

        // Speso requires a publicly reachable HTTPS callback. Anything else
        // (a local http:// APP_URL, for instance) would have the whole request
        // rejected, so drop it and let Speso fall back to the business default
        // — our own status polling covers delivery either way.
        if ($callbackUrl && ! str_starts_with($callbackUrl, 'https://')) {
            $callbackUrl = null;
        }

        return $this->post('/collections', array_filter([
            'order_id'     => $orderId,
            'mobile'       => $mobile,
            'network'      => $network,
            'amount'       => $amount,
            'description'  => $description,
            'callback_url' => $callbackUrl,
        ], fn ($value) => ! is_null($value)));
    }

    /** Check a collection by our order_id or Speso's own reference. */
    public function getCollection(string $reference): array
    {
        return $this->get('/collections/'.$reference);
    }

    /** Send a one-off SMS. */
    public function sendSms(string $message, ?string $phone = null, ?array $recipients = null, ?string $senderId = null): array
    {
        return $this->post('/messages/send', array_filter([
            'sender_id'  => $senderId ?? config('services.speso.sender_id'),
            'phone'      => $phone,
            'recipients' => $recipients,
            'message'    => $message,
        ], fn ($value) => ! is_null($value)));
    }

    private function post(string $path, array $payload): array
    {
        return $this->handle($this->client()->post($this->baseUrl.$path, $payload));
    }

    private function get(string $path): array
    {
        return $this->handle($this->client()->get($this->baseUrl.$path));
    }

    private function client(): PendingRequest
    {
        if (! $this->apiKey) {
            throw new RuntimeException('Speso API key is not configured. Set SPESO_API_KEY in .env.');
        }

        // A USSD gateway drops the session if we do not reply within a few
        // seconds, so fail fast with our own message rather than blowing past
        // that deadline waiting on a slow provider.
        return Http::withToken($this->apiKey)->acceptJson()->timeout(6);
    }

    private function handle(Response $response): array
    {
        $body = $response->json() ?? [];

        if ($response->failed() || ($body['success'] ?? true) === false) {
            Log::error('Speso API request failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            throw new RuntimeException($body['message'] ?? $body['error'] ?? 'Speso API request failed.');
        }

        return $body;
    }
}
