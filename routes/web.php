<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\VoteController;
use App\Models\Category;
use App\Models\Event;
use Illuminate\Support\Facades\Route;

// ─── Public voting portal ─────────────────────────────────────────────────────
Route::prefix('vote')->name('vote.')->group(function () {
    Route::get('/',              [VoteController::class, 'index'])->name('index');
    Route::get('/privacy',       [VoteController::class, 'privacy'])->name('privacy');
    Route::get('/confirmed',     [VoteController::class, 'confirmed'])->name('confirmed');
    Route::get('/callback',      [VoteController::class, 'paymentCallback'])->name('payment-callback');

    // Ballot page — guarded by voting window check
    Route::get('/events/{slug}', [VoteController::class, 'event'])
        ->middleware('vote.open')
        ->name('event');
});

Route::get('/privacy', fn() => redirect()->route('vote.privacy'));

// ─── Root redirect ────────────────────────────────────────────────────────────
Route::get('/', fn() => redirect()->route('vote.index'));

// ─── Admin Auth ───────────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:admin.login')->name('login.post');
    Route::post('logout',[AuthController::class, 'logout'])->name('logout')->middleware('auth:admin');

    // ── Protected admin routes ────────────────────────────────────────────────
    Route::middleware('auth:admin')->group(function () {

        Route::get('dashboard', fn() => view('admin.dashboard'))->name('dashboard');

        // Events CRUD
        Route::prefix('events')->name('events.')->group(function () {

            Route::get('/',       fn() => view('admin.events.index'))->name('index');
            Route::get('create',  fn() => view('admin.events.form'))->name('create');
            Route::get('{event}', function (Event $event) {
                abort_if(auth('admin')->user()->organization_id !== $event->organization_id, 403);
                return view('admin.events.show', compact('event'));
            })->name('show');
            Route::get('{event}/edit', function (Event $event) {
                abort_if(auth('admin')->user()->organization_id !== $event->organization_id, 403);
                return view('admin.events.form', compact('event'));
            })->name('edit');

            // Sub-pages
            Route::get('{event}/results', function (Event $event) {
                abort_if(auth('admin')->user()->organization_id !== $event->organization_id, 403);
                return view('admin.events.results', compact('event'));
            })->name('results');

            Route::get('{event}/payments', function (Event $event) {
                abort_if(auth('admin')->user()->organization_id !== $event->organization_id, 403);
                return view('admin.events.payments', compact('event'));
            })->name('payments');

            Route::get('{event}/fraud', function (Event $event) {
                abort_if(auth('admin')->user()->organization_id !== $event->organization_id, 403);
                return view('admin.events.fraud', compact('event'));
            })->name('fraud');

            Route::get('{event}/categories/{category}/nominees', function (Event $event, Category $category) {
                abort_if(auth('admin')->user()->organization_id !== $event->organization_id, 403);
                abort_if($category->event_id !== $event->id, 404);
                return view('admin.events.nominees', compact('event', 'category'));
            })->name('nominees');

            // Exports
            Route::prefix('{event}/export')->name('export.')->group(function () {
                Route::get('results-pdf',   [ExportController::class, 'resultsPdf'])->name('results-pdf');
                Route::get('payments-csv',  [ExportController::class, 'paymentsCsv'])->name('payments-csv');
                Route::get('audit-csv',     [ExportController::class, 'auditCsv'])->name('audit-csv');
            });
        });
    });
});
