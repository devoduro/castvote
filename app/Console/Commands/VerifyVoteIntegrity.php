<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Services\VoteIntegrityService;
use Illuminate\Console\Command;

class VerifyVoteIntegrity extends Command
{
    protected $signature   = 'votes:verify-integrity {event? : Event ID or slug (omit to check all live events)}';
    protected $description = 'Replay the payments ledger and flag any vote integrity violations';

    public function handle(VoteIntegrityService $integrity): int
    {
        $arg = $this->argument('event');

        $events = $arg
            ? collect([Event::where('id', $arg)->orWhere('slug', $arg)->firstOrFail()])
            : Event::where('status', 'live')->get();

        if ($events->isEmpty()) {
            $this->info('No live events to check.');
            return self::SUCCESS;
        }

        $totalViolations = 0;

        foreach ($events as $event) {
            $this->line("\n<fg=cyan>Checking: {$event->name} (ID {$event->id})</>");

            $violations = $integrity->verify($event);

            if ($violations->isEmpty()) {
                $this->info("  ✅  Ledger clean — no violations found.");
                continue;
            }

            $totalViolations += $violations->count();

            foreach ($violations as $v) {
                $icon = match ($v['severity']) {
                    'critical' => '<fg=red>🔴 CRITICAL</>',
                    'high'     => '<fg=red>🔴 HIGH</>',
                    'medium'   => '<fg=yellow>🟡 MEDIUM</>',
                    default    => '⚪ LOW',
                };
                $this->line("  {$icon}  [{$v['type']}] {$v['message']}");
            }
        }

        if ($totalViolations > 0) {
            $this->newLine();
            $this->error("Found {$totalViolations} violation(s). Review the audit log and run payments:reverify for uncredited payments.");
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
