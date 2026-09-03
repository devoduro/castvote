<?php

use App\Http\Controllers\PaystackWebhookController;
use App\Http\Controllers\Webhooks\SpesoWebhookController;
use App\Http\Controllers\Webhooks\UssdWebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| USSD Callback
|--------------------------------------------------------------------------
| The gateway POSTs here on every menu step. No auth middleware — gateways
| do not send a bearer token. Legitimacy is established by resolving the
| dialled shortcode to a live event; anything else is turned away.
| Rate-limited per IP to blunt replay/spam attempts.
*/
Route::post('/ussd/callback', UssdWebhookController::class)
    ->middleware('throttle:ussd')
    ->name('ussd.callback');

/*
|--------------------------------------------------------------------------
| Speso Collection Webhook
|--------------------------------------------------------------------------
| Speso POSTs the outcome of a Mobile Money collection here. The
| X-Speso-Signature HMAC is verified inside the controller before any
| state changes.
*/
Route::post('/webhooks/speso', SpesoWebhookController::class)
    ->middleware('throttle:paystack.webhook')
    ->name('webhooks.speso');

/*
|--------------------------------------------------------------------------
| Paystack Webhook
|--------------------------------------------------------------------------
| Paystack POSTs charge events here — still the provider behind the web
| ballot. Signature verified inside the controller before any state
| mutation occurs.
*/
Route::post('/webhooks/paystack', [PaystackWebhookController::class, 'handle'])
    ->middleware('throttle:paystack.webhook')
    ->name('webhooks.paystack');
