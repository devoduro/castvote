<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\Event;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AnonymiseVoterPhones extends Command
{
    protected $signature   = 'votes:anonymise {event : Event ID or slug} {--force : Skip confirmation prompt}';
    protected $description = 'Irreversibly null voter_phone from the votes table for anonymous_tally events (Ghana DPA Act 843 compliance)';

    public function handle(): int
    {
        $arg   = $this->argument('event');
        $event = Event::where('id', $arg)->orWhere('slug', $arg)->firstOrFail();

        // Only valid for anonymous_tally events
        if (!$event->isAnonymousTally()) {
            $this->error("Event '{$event->name}' does not have anonymous_tally enabled. Nothing to do.");
            return self::FAILURE;
        }

        // Only valid after event closes
        if ($event->status !== 'closed') {
            $this->error("Event must be in 'closed' status before anonymising. Current status: {$event->status}");
            return self::FAILURE;
        }

        // Count affected rows
        $affected = \App\Models\Vote::where('event_id', $event->id)
            ->whereNotNull('voter_phone')
            ->count();

        if ($affected === 0) {
            $this->info('No voter phones to anonymise — already done or no votes exist.');
            return self::SUCCESS;
        }

        $this->warn("⚠️  This will IRREVERSIBLY null voter_phone for {$affected} vote row(s) in '{$event->name}'.");
        $this->warn("    This action cannot be undone. It is logged in the audit trail.");

        if (!$this->option('force') && !$this->confirm('Proceed with anonymisation?')) {
            $this->info('Cancelled.');
            return self::SUCCESS;
        }

        DB::transaction(function () use ($event, $affected) {
            // Null out voter_phone on votes (the only voter-linkage field)
            \App\Models\Vote::where('event_id', $event->id)
                ->whereNotNull('voter_phone')
                ->update(['voter_phone' => null]);

            // Record the irreversible action in the audit log
            AuditLog::record(
                action:  'votes.voter_phones_anonymised',
                subject: $event,
                meta: [
                    'rows_affected'    => $affected,
                    'anonymised_at'    => now()->toIso8601String(),
                    'operator'         => 'artisan:votes:anonymise',
                    'dpa_act'          => 'Ghana Data Protection Act 2012 (Act 843)',
                    'retention_policy' => 'voter_phone nulled at event close per DPA compliance',
                ],
                adminId: null,
            );
        });

        $this->info("✅  Done. Anonymised voter_phone on {$affected} vote rows for '{$event->name}'.");
        $this->info("    Payment records (for financial reconciliation) retain phone numbers per 7-year retention policy.");

        return self::SUCCESS;
    }
}
