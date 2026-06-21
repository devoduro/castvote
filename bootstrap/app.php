<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        // ── CSRF exclusions ───────────────────────────────────────────────────
        // Arkesel and Paystack POST from their servers, not from a browser session.
        $middleware->validateCsrfTokens(except: [
            'api/ussd/callback',
            'api/webhooks/paystack',
        ]);

        // ── Named middleware aliases ──────────────────────────────────────────
        $middleware->alias([
            'admin'          => \App\Http\Middleware\AdminAuthenticated::class,
            'vote.open'      => \App\Http\Middleware\VotingWindowOpen::class,
            'security.headers' => \App\Http\Middleware\SecurityHeaders::class,
        ]);

        // ── Global middleware ─────────────────────────────────────────────────
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);

        // ── Throttle rate limiters ────────────────────────────────────────────
        // USSD callback: 120 req/min per IP (already on route, but enforce globally too)
        // Web vote checkout: defined in RateLimiterServiceProvider

    })
    ->withExceptions(function (Exceptions $exceptions): void {

        // Return JSON for API routes on validation errors
        $exceptions->shouldRenderJsonWhen(
            fn(Request $request) => $request->is('api/*')
        );

    })->create();
