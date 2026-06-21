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
        // USSD callback — 120 req/min per IP (Arkesel may burst on session steps)
        RateLimiter::for('ussd', function (Request $request) {
            return Limit::perMinute(120)->by($request->ip());
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
}
