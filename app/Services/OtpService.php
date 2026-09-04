<?php

namespace App\Services;

use App\Models\OtpCode;
use Illuminate\Support\Facades\Log;

/**
 * One-time codes over SMS.
 *
 * Uses Speso's OTP API when SPESO_API_KEY and SPESO_SENDER_ID are both set —
 * Speso generates, sends and verifies the code, so nothing is stored locally.
 * Without those, it falls back to a local code so the flow still works in
 * development; that code is logged rather than texted.
 */
class OtpService
{
    /** Wrong guesses allowed against a locally-issued code. */
    private const MAX_ATTEMPTS = 5;

    public const TTL_MINUTES = 10;

    /**
     * Send a code to a phone number.
     *
     * @return string|null The code, only when the local fallback was used;
     *                     Speso never discloses the code it generated.
     */
    public function send(string $phone, string $purpose, ?string $message = null): ?string
    {
        if ($this->usingSpeso()) {
            // SpesoClient throws on failure (rate limiting, bad number);
            // callers catch that and show their own message.
            SpesoClient::make()->requestOtp(
                phone:   $phone,
                digits:  6,
                message: $message,
            );

            return null;
        }

        // Supersede any code still outstanding for this phone and purpose.
        OtpCode::where('phone', $phone)
            ->where('purpose', $purpose)
            ->whereNull('consumed_at')
            ->update(['consumed_at' => now()]);

        $otp = OtpCode::create([
            'phone'      => $phone,
            'code'       => (string) random_int(100000, 999999),
            'purpose'    => $purpose,
            'expires_at' => now()->addMinutes(self::TTL_MINUTES),
        ]);

        Log::info("OTP for {$phone} ({$purpose}): {$otp->code}");

        return $otp->code;
    }

    public function verify(string $phone, string $purpose, string $code): bool
    {
        if ($this->usingSpeso()) {
            try {
                $result = SpesoClient::make()->verifyOtp(phone: $phone, code: $code);
            } catch (\Throwable $e) {
                report($e);

                return false;
            }

            return (bool) ($result['success'] ?? false)
                && (bool) ($result['data']['verified'] ?? $result['verified'] ?? false);
        }

        $otp = OtpCode::where('phone', $phone)
            ->where('purpose', $purpose)
            ->whereNull('consumed_at')
            ->where('expires_at', '>=', now())
            ->latest()
            ->first();

        if (! $otp) {
            return false;
        }

        // Burn the code once it has been guessed at too many times, so a
        // six-digit code cannot be brute forced within its ten-minute life.
        if ($otp->attempts >= self::MAX_ATTEMPTS) {
            $otp->update(['consumed_at' => now()]);

            return false;
        }

        if (! hash_equals($otp->code, $code)) {
            $otp->increment('attempts');

            return false;
        }

        $otp->update(['consumed_at' => now()]);

        return true;
    }

    /** Whether codes are handled by Speso rather than the local fallback. */
    public function usingSpeso(): bool
    {
        return filled(config('services.speso.api_key'))
            && filled(config('services.speso.sender_id'));
    }
}
