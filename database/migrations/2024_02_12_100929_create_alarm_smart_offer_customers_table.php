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
        Schema::create('alarm_smart_offer_customers', function (Blueprint $table) {
            $table->id();
            $table->boolean('status')->default(1);
            $table->foreignId('alarm_smart_execute_id')->constrained('alarm_smart_executes')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('alarm_smart_category_id')->constrained('alarm_smart_categories')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('alarm_status_id')->constrained('alarm_statuses')->cascadeOnUpdate()->restrictOnDelete();
            $table->boolean('product_id_one_status')->default(1);
            $table->foreignId('as_product_id_one')->nullable()->constrained('alarm_smart_offer_products')->cascadeOnUpdate()->restrictOnDelete();
            $table->boolean('product_id_two_status')->default(0);
            $table->foreignId('as_product_id_two')->nullable()->constrained('alarm_smart_offer_products')->cascadeOnUpdate()->restrictOnDelete();
            $table->boolean('product_id_tree_status')->default(0);
            $table->foreignId('as_product_id_tree')->nullable()->constrained('alarm_smart_offer_products')->cascadeOnUpdate()->restrictOnDelete();
            $table->boolean('category_id_one_status')->default(1);
            $table->foreignId('as_category_id_one')->nullable()->constrained('alarm_smart_offer_categories')->cascadeOnUpdate()->restrictOnDelete();
            $table->boolean('category_id_two_status')->default(0);
            $table->foreignId('as_category_id_two')->nullable()->constrained('alarm_smart_offer_categories')->cascadeOnUpdate()->restrictOnDelete();
            $table->boolean('category_id_tree_status')->default(0);
            $table->foreignId('as_category_id_tree')->nullable()->constrained('alarm_smart_offer_categories')->cascadeOnUpdate()->restrictOnDelete();
            $table->boolean('setting_edit')->default(0);
            $table->text('setting_send_date')->nullable();
            $table->text('setting_send_time')->nullable();
            $table->integer('setting_discount_status_id')->default(1)->constrained('discount_statuses')->cascadeOnUpdate()->restrictOnDelete();
            $table->integer('setting_product_count')->default(1);
            $table->integer('setting_category_count')->default(1);
            $table->boolean('setting_sms')->default(1);
            $table->boolean('setting_notification')->default(1);
            $table->boolean('setting_email')->default(1);
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
        // Schema::dropIfExists('alarm_smart_offer_customers');
    }
};
