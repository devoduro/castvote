<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class RateLimiterServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // USSD callback.
        //
        // Every request from a gateway shares that gateway's IP, so a per-IP
        // limit is really a limit on the whole platform: 200 callers taking
        // five steps each is 1,000 requests a minute from one address. The old
        // 120/min ceiling would have returned 429 to genuine dials, and a 429
        // is rejected before the controller runs, so it would never appear in
        // the gateway log either — a failure invisible from the admin panel.
        //
        // So the abuse limit is per caller, where flooding actually happens,
        // and the per-IP limit is only a backstop sized for a busy shortcode.
        RateLimiter::for('ussd', function (Request $request) {
            $limits = [Limit::perMinute(2000)->by('ussd-ip:'.$request->ip())];

            if ($msisdn = self::callerKey($request)) {
                // A whole session is roughly ten requests; 40 is generous.
                array_unshift($limits, Limit::perMinute(40)->by('ussd-msisdn:'.$msisdn));
            }

            return $limits;
        });

        // USSD self-test page — each hit is four callback requests, so keep
        // it to a handful per minute per IP. It's for a support engineer with
        // a browser, not for polling.
        RateLimiter::for('ussd.selftest', function (Request $request) {
            return Limit::perMinute(6)->by('ussd-selftest:'.$request->ip());
        });

        // Web vote checkout — 20 attempts per phone per 10 minutes
        // Prevents scripted vote-buying loops from the web portal
        RateLimiter::for('vote.checkout', function (Request $request) {
            $phone = $request->input('phone', $request->ip());
            return [
                Limit::perMinutes(10, 20)->by('phone:' . $phone),
                Limit::perMinutes(10, 60)->by('ip:' . $request->ip()),
            ];
        });

        // Admin login — 10 attempts per 5 minutes per IP (brute-force guard)
        RateLimiter::for('admin.login', function (Request $request) {
            return Limit::perMinutes(5, 10)->by($request->ip());
        });

        // Paystack webhook — 200 req/min per IP (their retry bursts)
        RateLimiter::for('paystack.webhook', function (Request $request) {
            return Limit::perMinute(200)->by($request->ip());
        });
    }

    /**
     * The dialling number, whatever the gateway calls it and however it is
     * cased. Returns null when the payload carries no number to key on.
     */
    private static function callerKey(Request $request): ?string
    {
        $data = array_change_key_case($request->all(), CASE_LOWER);

        foreach (['msisdn', 'phonenumber', 'phone_number'] as $key) {
            if (filled($data[$key] ?? null)) {
                return preg_replace('/\D+/', '', (string) $data[$key]) ?: null;
            }
        }

        return null;
    }
}
