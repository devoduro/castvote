<?php

use App\Http\Controllers\UssdController;
use App\Http\Controllers\PaystackWebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| USSD Webhook (Arkesel)
|--------------------------------------------------------------------------
| Arkesel POSTs here on every menu step. No auth middleware — Arkesel
| does not send a bearer token. We verify legitimacy by checking that
| the sessionId resolves to a known event (service code lookup).
| Rate-limited per IP to blunt any replay/spam attempts.
*/
Route::post('/ussd/callback', [UssdController::class, 'handle'])
    ->middleware('throttle:ussd')
    ->name('ussd.callback');

/*
|--------------------------------------------------------------------------
| Paystack Webhook
|--------------------------------------------------------------------------
| Paystack POSTs charge events here. Signature verified inside the
| controller before any state mutation occurs.
*/
Route::post('/webhooks/paystack', [PaystackWebhookController::class, 'handle'])
    ->middleware('throttle:paystack.webhook')
    ->name('webhooks.paystack');
