<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('history_customer_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_history_id')->constrained('history_customer_devices')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->text('category_name')->nullable();
            $table->text('search')->nullable();
            $table->text('filter')->nullable();
            $table->text('hashtag')->nullable();
            $table->text('sort')->nullable();
            $table->integer('sub_category_count')->default(0);
            $table->integer('product_count')->default(0);
            $table->text('page_view_time')->nullable();
            $table->text('time_start')->nullable();
            $table->text('time_end')->nullable();
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
        Schema::dropIfExists('history_customer_categories');
    }
};
