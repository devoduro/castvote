<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->string('action', 100)->comment('e.g. event.status_changed, voter_phones.anonymised, nominee.deleted');
            $table->string('subject_type', 100)->nullable()->comment('Model class name');
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->json('meta')->nullable()->comment('Before/after values, IP address, etc.');
            $table->ipAddress('ip_address')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['subject_type', 'subject_id']);
            $table->index(['admin_id', 'created_at']);
            $table->index('action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_log');
    }
};
