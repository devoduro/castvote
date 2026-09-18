<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\RegisterController;
use App\Http\Controllers\AwardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NomineeController;
use App\Http\Controllers\ResultsController;
use App\Http\Controllers\VoteController;
use App\Models\Category;
use App\Models\Event;
use Illuminate\Support\Facades\Route;

// ─── Public site ──────────────────────────────────────────────────────────────
Route::get('/',      [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');

// Awards = award-type campaigns; Events = every public campaign type.
Route::get('/awards', [AwardController::class, 'awards'])->name('awards.index');
Route::get('/events', [AwardController::class, 'index'])->name('events.index');

Route::get('/awards/{slug}',                        [AwardController::class, 'show'])->name('awards.show');
Route::get('/awards/{slug}/categories/{category}',  [AwardController::class, 'category'])->name('awards.category');

Route::get('/nominees',            [NomineeController::class, 'index'])->name('nominees.index');
Route::get('/nominees/{nominee}',  [NomineeController::class, 'show'])->name('nominees.show');

// Focused voting entry point: pick an award, then vote in it.
Route::get('/voting', [AwardController::class, 'voting'])->name('voting.index');

Route::get('/results',         [ResultsController::class, 'index'])->name('results.index');
Route::get('/results/{slug}',  [ResultsController::class, 'show'])->name('results.show');

// ─── Public voting portal ─────────────────────────────────────────────────────
Route::prefix('vote')->name('vote.')->group(function () {
    Route::get('/',              [VoteController::class, 'index'])->name('index');
    Route::get('/privacy',       [VoteController::class, 'privacy'])->name('privacy');
    // Both shapes resolve: ?ref= is what the ballot redirects to, and the
    // path form is friendlier to share or reopen from a receipt.
    Route::get('/confirmed/{reference?}', [VoteController::class, 'confirmed'])->name('confirmed');
    Route::get('/callback',      [VoteController::class, 'paymentCallback'])->name('payment-callback');
    Route::get('/receipt/{reference}', [VoteController::class, 'receipt'])->name('receipt');

    // Ballot page — guarded by voting window check
    Route::get('/events/{slug}', [VoteController::class, 'event'])
        ->middleware('vote.open')
        ->name('event');
});

Route::get('/privacy', fn() => redirect()->route('vote.privacy'));

// ─── Admin Auth ───────────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('login',    [AuthController::class,  'showLogin'])->name('login');
    Route::post('login',   [AuthController::class,  'login'])->middleware('throttle:admin.login')->name('login.post');
    Route::post('logout',  [AuthController::class,  'logout'])->name('logout')->middleware('auth:admin');
    Route::get('register', [RegisterController::class, 'show'])->name('register');
    Route::post('register',[RegisterController::class, 'store'])->name('register.post');
    Route::get('verify-email/{id}/{token}', [RegisterController::class, 'verifyEmail'])->name('verify-email');

    // Password reset by SMS one-time code (Speso OTP).
    Route::get('forgot-password', fn () => view('auth.forgot-password'))
        ->middleware('throttle:admin.login')
        ->name('password.request');

    // ── Protected admin routes ────────────────────────────────────────────────
    Route::middleware('auth:admin')->group(function () {

        Route::get('dashboard',    fn() => view('admin.dashboard'))->name('dashboard');
        Route::get('nominations',  fn() => view('admin.nominations'))->name('nominations');
        Route::get('vote-results', fn() => view('admin.vote-results'))->name('vote-results');
        Route::get('transactions', fn() => view('admin.transactions'))->name('transactions');
        Route::get('earnings',     fn() => view('admin.earnings'))->name('earnings');
        Route::get('profile',      fn() => view('admin.profile'))->name('profile');
        Route::get('audit',        fn() => view('admin.audit'))->name('audit');

        Route::get('approvals', function () {
            abort_unless(auth('admin')->user()?->isSuperAdmin(), 403);
            return view('admin.approvals');
        })->name('approvals');

        // Platform-wide USSD service settings, routing and simulator.
        Route::get('ussd', function () {
            abort_unless(auth('admin')->user()?->isSuperAdmin(), 403);
            return view('admin.ussd');
        })->name('ussd');

        // Events CRUD
        Route::prefix('events')->name('events.')->group(function () {

            Route::get('/',       fn() => view('admin.events.index'))->name('index');
            Route::get('create',  fn() => view('admin.events.form'))->name('create');
            Route::get('{event}', function (Event $event) {
                abort_unless(auth('admin')->user()->canAccessEvent($event), 403);
                return view('admin.events.show', compact('event'));
            })->name('show');
            Route::get('{event}/edit', function (Event $event) {
                abort_unless(auth('admin')->user()->canAccessEvent($event), 403);
                return view('admin.events.form', compact('event'));
            })->name('edit');

            // Sub-pages
            Route::get('{event}/results', function (Event $event) {
                abort_unless(auth('admin')->user()->canAccessEvent($event), 403);
                return view('admin.events.results', compact('event'));
            })->name('results');

            Route::get('{event}/payments', function (Event $event) {
                abort_unless(auth('admin')->user()->canAccessEvent($event), 403);
                return view('admin.events.payments', compact('event'));
            })->name('payments');

            Route::get('{event}/fraud', function (Event $event) {
                abort_unless(auth('admin')->user()->canAccessEvent($event), 403);
                return view('admin.events.fraud', compact('event'));
            })->name('fraud');

            Route::get('{event}/categories/{category}/nominees', function (Event $event, Category $category) {
                abort_unless(auth('admin')->user()->canAccessEvent($event), 403);
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
