<?php

namespace App\Providers;

use App\Services\ArkeselSmsService;
use App\Services\PaymentInitiationService;
use App\Services\PaystackWebhookService;
use App\Services\SpesoClient;
use App\Services\VoteCreditService;
use App\Services\VoteIntegrityService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ArkeselSmsService::class);
        $this->app->singleton(PaystackWebhookService::class);
        $this->app->singleton(PaymentInitiationService::class);
        $this->app->singleton(VoteIntegrityService::class);
        $this->app->singleton(VoteCreditService::class);
        $this->app->singleton(SpesoClient::class, fn () => SpesoClient::make());
    }

    public function boot(): void
    {
        // Force HTTPS URLs in production
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        // Paginate with the ClickVote design system rather than Laravel's default markup.
        Paginator::defaultView('pagination.clickvote');
        Paginator::defaultSimpleView('pagination.clickvote');
    }
}
