<?php

use App\Console\Commands\AnonymiseVoterPhones;
use App\Console\Commands\ReverifyPendingPayments;
use App\Console\Commands\VerifyVoteIntegrity;
use App\Models\Event;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Re-verify payments that may have missed their webhook — every 2 hours
Schedule::command(ReverifyPendingPayments::class, ['--hours=2'])->everyTwoHours();

// Verify vote ledger integrity for all live events — daily at 03:00
Schedule::command(VerifyVoteIntegrity::class)->dailyAt('03:00');

// Auto-anonymise voter phones for anonymous_tally events that just closed
// Runs hourly and checks for newly-closed events needing anonymisation
Schedule::call(function () {
    Event::where('status', 'closed')
        ->whereRaw("JSON_EXTRACT(voting_rules, '$.anonymous_tally') = true")
        ->whereHas('votes', fn($q) => $q->whereNotNull('voter_phone'))
        ->each(function (Event $event) {
            Artisan::call(AnonymiseVoterPhones::class, [
                'event'   => $event->id,
                '--force' => true,
            ]);
        });
})->hourly()->name('auto-anonymise-closed-elections');
