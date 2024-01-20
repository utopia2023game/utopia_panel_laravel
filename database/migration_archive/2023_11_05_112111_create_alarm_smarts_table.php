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
        Schema::create('alarm_smarts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('alarm_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('hc_analytics_id')->constrained('history_customer_analytics_customers')->cascadeOnUpdate()->restrictOnDelete();
            $table->integer('product_count')->default(1);
            $table->foreignId('product_id_one')->nullable()->constrained('products')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('product_id_two')->nullable()->constrained('products')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('product_id_tree')->nullable()->constrained('products')->cascadeOnUpdate()->restrictOnDelete();
            $table->integer('category_count')->default(1);
            $table->foreignId('category_id_one')->nullable()->constrained('categories')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('category_id_two')->nullable()->constrained('categories')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('category_id_tree')->nullable()->constrained('categories')->cascadeOnUpdate()->restrictOnDelete();
            $table->boolean('status')->default(1);
            $table->boolean('priority')->default(0);
            $table->text('discount_tag')->nullable();
            $table->boolean('send_sms')->default(0);
            $table->text('send_time')->nullable();
            $table->text('send_date')->nullable();
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
        Schema::dropIfExists('alarm_smarts');
    }
};
