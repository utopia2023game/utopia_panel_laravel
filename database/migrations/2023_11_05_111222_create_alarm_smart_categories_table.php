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
        Schema::create('alarm_smart_categories', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->text('name_fa');
            $table->boolean('status')->default(1);
            $table->boolean('priority')->default(0);
            $table->integer('delay_day_execute')->default(10);
            $table->integer('product_count')->default(1);
            $table->integer('category_count')->default(1);
            $table->text('discount_tag')->nullable();
            $table->text('send_time')->nullable();
            $table->boolean('send_sms')->default(1);
            $table->boolean('send_notification')->default(1);
            $table->boolean('send_email')->default(1);
            $table->text('icon')->nullable();
            $table->text('image')->nullable();
            $table->text('description')->nullable();
            $table->timestampsTz();
            $table->softDeletesTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('alarm_smart_categories');
    }
};
