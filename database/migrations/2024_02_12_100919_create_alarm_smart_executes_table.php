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
        Schema::create('alarm_smart_executes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alarm_smart_category_id')->constrained('alarm_smart_categories')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('alarm_status_id')->constrained('alarm_statuses')->cascadeOnUpdate()->restrictOnDelete();
            $table->boolean('status')->default(1);
            $table->text('date')->nullable();
            $table->text('setting_send_time')->nullable();
            $table->integer('setting_discount_status_id')->default(1)->constrained('discount_statuses')->cascadeOnUpdate()->restrictOnDelete();
            $table->text('setting_product_count')->nullable();
            $table->text('setting_category_count')->nullable();
            $table->boolean('setting_sms')->default(1);
            $table->boolean('setting_notification')->default(1);
            $table->boolean('setting_email')->default(1);
            $table->timestampsTz();
            $table->softDeletesTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alarm_smart_executes');
    }
};
