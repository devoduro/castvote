<?php

namespace App\Providers;

use App\Services\ArkeselSmsService;
use App\Services\PaymentInitiationService;
use App\Services\PaystackWebhookService;
use App\Services\UssdSessionService;
use App\Services\VoteIntegrityService;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ArkeselSmsService::class);
        $this->app->singleton(UssdSessionService::class);
        $this->app->singleton(PaystackWebhookService::class);
        $this->app->singleton(PaymentInitiationService::class);
        $this->app->singleton(VoteIntegrityService::class);
    }

    public function boot(): void
    {
        // Force HTTPS URLs in production
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
}
