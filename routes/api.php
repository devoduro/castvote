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
| Rate-limited per caller to blunt replay/spam attempts.
|
| GET and HEAD are answered with a small health payload rather than 405,
| because gateway portals commonly validate an endpoint URL by fetching it
| when it is saved, and a 405 reads as a broken endpoint. It exposes nothing:
| the body is a fixed status string.
*/
Route::match(['get', 'head', 'post'], '/ussd/callback', UssdWebhookController::class)
    ->middleware('throttle:ussd')
    ->name('ussd.callback');

/*
|--------------------------------------------------------------------------
| USSD Self-Test
|--------------------------------------------------------------------------
| Walks a four-screen session against the callback above, in-process, and
| prints a plain-text conformance report. Meant to be opened in a browser by
| the gateway's support staff when they ask "is your endpoint working?".
| Shows nothing the callback does not already show; throttled because each
| hit is four requests' worth of work.
*/
Route::get('/ussd/selftest', function () {
    $run = \App\Ussd\Support\SelfTest::run();

    return response(\App\Ussd\Support\SelfTest::report($run), $run['passed'] ? 200 : 500)
        ->header('Content-Type', 'text/plain; charset=UTF-8')
        ->header('Cache-Control', 'no-store');
})->middleware('throttle:ussd.selftest')->name('ussd.selftest');

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
