<?php

namespace App\Http\Controllers;

use App\Services\PaystackWebhookService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class PaystackWebhookController extends Controller
{
    public function __construct(private readonly PaystackWebhookService $webhookService) {}

    public function handle(Request $request): Response
    {
        // --- 1. Verify signature BEFORE parsing JSON ---
        $rawBody   = $request->getContent();
        $signature = $request->header('X-Paystack-Signature', '');

        if (!$this->webhookService->verifySignature($rawBody, $signature)) {
            Log::warning('Paystack webhook signature mismatch', [
                'ip'        => $request->ip(),
                'signature' => $signature,
            ]);
            // Return 200 anyway — a 4xx causes Paystack to retry indefinitely
            // but we log and take no action on unsigned payloads.
            return response('Invalid signature', 200);
        }

        // --- 2. Parse payload ---
        $payload = json_decode($rawBody, true);

        if (!is_array($payload)) {
            Log::warning('Paystack webhook invalid JSON', ['ip' => $request->ip()]);
            return response('Bad payload', 200);
        }

        Log::info('Paystack webhook received', ['event' => $payload['event'] ?? 'unknown']);

        // --- 3. Dispatch to service ---
        try {
            $this->webhookService->handle($payload);
        } catch (\Throwable $e) {
            // Log but return 200 so Paystack does not keep retrying a broken handler
            Log::error('Paystack webhook handler exception', [
                'event'   => $payload['event'] ?? '',
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);
        }

        // Always return 200 to Paystack — if we return 4xx/5xx they will retry
        return response('OK', 200);
    }
}
