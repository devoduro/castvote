<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->enum('account_status', ['pending', 'approved', 'rejected'])
                  ->default('approved')
                  ->after('role');
            $table->boolean('is_superadmin')->default(false)->after('account_status');
            $table->string('phone', 20)->nullable()->after('email');
            $table->index('account_status');
        });

        Schema::table('organizations', function (Blueprint $table) {
            $table->string('phone', 20)->nullable()->after('contact_email');
            $table->string('website')->nullable()->after('phone');
            $table->string('logo_path')->nullable()->after('website');
        });
    }

    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn(['account_status', 'is_superadmin', 'phone']);
        });
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn(['phone', 'website', 'logo_path']);
        });
    }
};
