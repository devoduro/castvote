<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Services\PaystackWebhookService;
use Illuminate\Console\Command;

class ReverifyPendingPayments extends Command
{
    protected $signature   = 'payments:reverify {--hours=2 : Only check payments older than N hours}';
    protected $description = 'Re-verify pending Paystack payments that may have missed their webhook';

    public function handle(PaystackWebhookService $webhookService): int
    {
        $hours = (int) $this->option('hours');

        $pending = Payment::where('status', 'pending')
            ->where('provider', 'paystack')
            ->where('created_at', '<', now()->subHours($hours))
            ->get();

        if ($pending->isEmpty()) {
            $this->info("No pending payments older than {$hours} hours.");
            return self::SUCCESS;
        }

        $this->info("Found {$pending->count()} pending payment(s) to re-verify...");
        $bar = $this->output->createProgressBar($pending->count());
        $bar->start();

        $resolved = 0;

        foreach ($pending as $payment) {
            $result = $webhookService->reverify($payment);
            if ($result) {
                $resolved++;
            }
            $bar->advance();
            // Avoid hammering Paystack API
            usleep(300_000); // 300ms between calls
        }

        $bar->finish();
        $this->newLine();
        $this->info("Resolved: {$resolved} / {$pending->count()}");

        return self::SUCCESS;
    }
}
