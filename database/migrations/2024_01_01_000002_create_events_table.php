<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('event_type', ['award', 'agm', 'election']);
            $table->json('voting_rules')->comment('pay_per_vote, price_per_vote_pesewas, max_votes_per_voter, requires_eligibility_list, anonymous_tally');
            $table->string('ussd_shortcode')->nullable()->comment('e.g. *920*134*240#');
            $table->string('ussd_short_id')->nullable()->comment('Arkesel service/extension code');
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->enum('status', ['draft', 'live', 'closed'])->default('draft');
            $table->timestamps();

            $table->index(['status', 'starts_at', 'ends_at']);
            $table->index('ussd_short_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
