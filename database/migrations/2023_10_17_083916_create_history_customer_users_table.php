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
        Schema::create('history_customer_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_history_id')->constrained('history_customer_devices')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->text('tab')->nullable();
            $table->text('tag')->nullable();
            $table->text('last_value')->nullable();
            $table->text('submit_value')->nullable();
            $table->text('execute_time')->nullable();
            $table->text('status')->nullable();
            $table->text('message')->nullable();
            $table->timestampsTz();
            $table->softDeletesTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('history_customer_users');
    }
};
