<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ArkeselSmsService
{
    /**
     * Send a single SMS via Arkesel's SMS API.
     * Silently logs failures — never throw from here since this is
     * called inside payment webhook handlers and must not roll back
     * a successful payment just because SMS delivery failed.
     */
    public function send(string $phone, string $message): bool
    {
        // Normalise phone to international format for Arkesel
        $international = $this->toInternational($phone);

        try {
            $response = Http::withHeaders([
                'api-key' => config('services.arkesel.api_key'),
            ])->post('https://sms.arkesel.com/api/v2/sms/send', [
                'sender'     => config('services.arkesel.sender_id'),
                'message'    => $message,
                'recipients' => [$international],
            ]);

            if ($response->failed()) {
                Log::warning('Arkesel SMS failed', [
                    'phone'  => $international,
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return false;
            }

            Log::info('SMS sent', ['phone' => $international]);
            return true;

        } catch (\Throwable $e) {
            Log::error('Arkesel SMS exception', [
                'phone'   => $international,
                'message' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Send to multiple recipients (bulk — e.g. event broadcast).
     */
    public function sendBulk(array $phones, string $message): bool
    {
        $recipients = array_map(fn($p) => $this->toInternational($p), $phones);

        try {
            $response = Http::withHeaders([
                'api-key' => config('services.arkesel.api_key'),
            ])->post('https://sms.arkesel.com/api/v2/sms/send', [
                'sender'     => config('services.arkesel.sender_id'),
                'message'    => $message,
                'recipients' => $recipients,
            ]);

            if ($response->failed()) {
                Log::warning('Arkesel bulk SMS failed', [
                    'count'  => count($recipients),
                    'status' => $response->status(),
                ]);
                return false;
            }

            return true;

        } catch (\Throwable $e) {
            Log::error('Arkesel bulk SMS exception', ['message' => $e->getMessage()]);
            return false;
        }
    }

    private function toInternational(string $phone): string
    {
        // Strip spaces and dashes
        $phone = preg_replace('/[\s\-]/', '', $phone);

        // Already international
        if (str_starts_with($phone, '+233')) {
            return $phone;
        }
        if (str_starts_with($phone, '233')) {
            return '+' . $phone;
        }
        // Local 0XX format → +233XX
        if (str_starts_with($phone, '0')) {
            return '+233' . substr($phone, 1);
        }

        return $phone;
    }
}
