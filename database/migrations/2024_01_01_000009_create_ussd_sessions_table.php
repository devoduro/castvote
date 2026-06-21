<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ussd_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('arkesel_session_id')->unique();
            $table->foreignId('event_id')->nullable()->constrained()->nullOnDelete();
            $table->string('phone_number', 20);
            $table->string('current_step', 50)->default('welcome');
            $table->json('payload')->nullable()->comment('Accumulated inputs: category_code, nominee_id, quantity, etc.');
            $table->enum('status', ['active', 'completed', 'expired'])->default('active');
            $table->timestamps();

            $table->index(['arkesel_session_id', 'status']);
            $table->index('phone_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ussd_sessions');
    }
};
