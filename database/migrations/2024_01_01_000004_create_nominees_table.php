<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nominees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code', 10)->comment('Short numeric code, unique per category');
            $table->string('photo_path')->nullable();
            $table->text('bio')->nullable();
            $table->unsignedInteger('display_order')->default(0);
            $table->timestamps();

            $table->unique(['category_id', 'code']);
            $table->index(['category_id', 'display_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nominees');
    }
};
