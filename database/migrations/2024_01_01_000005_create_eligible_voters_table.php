<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eligible_voters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('identifier')->comment('Phone number or student index number or shareholder ID');
            $table->boolean('eligible')->default(true);
            $table->timestamp('voted_at')->nullable();
            $table->timestamps();

            $table->unique(['event_id', 'identifier']);
            $table->index(['event_id', 'eligible']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eligible_voters');
    }
};
