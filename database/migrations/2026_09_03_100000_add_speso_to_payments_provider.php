<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * payments.provider was an enum of ('paystack','arkesel'), so a Speso
 * collection could not be recorded at all. Widening it to a string keeps
 * every existing row valid and stops the next provider needing a migration.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE payments MODIFY provider VARCHAR(32) NOT NULL");

            return;
        }

        Schema::table('payments', function ($table) {
            $table->string('provider', 32)->change();
        });
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE payments MODIFY provider ENUM('paystack','arkesel') NOT NULL");

            return;
        }

        Schema::table('payments', function ($table) {
            $table->string('provider')->change();
        });
    }
};
