<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->enum('provider', ['paystack', 'arkesel']);
            $table->string('provider_reference')->unique()->comment('Idempotency key — unique per charge attempt');
            $table->unsignedBigInteger('amount_pesewas')->comment('Amount in pesewas, never floats');
            $table->string('currency', 3)->default('GHS');
            $table->string('phone_number', 20);
            $table->enum('momo_network', ['mtn', 'vodafone', 'airteltigo'])->nullable();
            $table->enum('status', ['pending', 'success', 'failed', 'reversed'])->default('pending');
            $table->json('raw_webhook_payload')->nullable()->comment('Full provider payload stored for audit');
            $table->json('metadata')->nullable()->comment('Stores nominee_id, category_id, quantity for vote crediting');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index(['event_id', 'status']);
            $table->index(['phone_number', 'created_at']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
