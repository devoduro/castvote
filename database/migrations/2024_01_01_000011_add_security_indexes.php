<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ensure only one vote row per payment — DB-level idempotency guard
        // (application logic already prevents duplicates, this is the safety net)
        Schema::table('votes', function (Blueprint $table) {
            $table->unique('payment_id', 'votes_payment_id_unique');
        });

        // Composite index for fraud panel queries (phone + event + time)
        Schema::table('votes', function (Blueprint $table) {
            $table->index(['voter_phone', 'event_id', 'created_at'], 'votes_fraud_lookup');
        });

        // Index for fast USSD session lookup during voting window
        Schema::table('events', function (Blueprint $table) {
            $table->index(['ussd_short_id', 'status'], 'events_ussd_lookup');
        });
    }

    public function down(): void
    {
        Schema::table('votes', function (Blueprint $table) {
            $table->dropUnique('votes_payment_id_unique');
            $table->dropIndex('votes_fraud_lookup');
        });
        Schema::table('events', function (Blueprint $table) {
            $table->dropIndex('events_ussd_lookup');
        });
    }
};
