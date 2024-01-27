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
        Schema::create('history_customer_order_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->text('order_times')->nullable();
            $table->text('order_last_date')->nullable();
            $table->text('all_count')->nullable();
            $table->text('all_avg_pay_price')->nullable();
            $table->text('all_avg_discount')->nullable();
            $table->text('all_count_discount')->nullable();
            $table->text('all_purchase_sequence_day')->nullable();
            $table->text('pending_count')->nullable();
            $table->text('pending_avg_pay_price')->nullable();
            $table->text('pending_avg_discount')->nullable();
            $table->text('pending_count_discount')->nullable();
            $table->text('pending_purchase_sequence_day')->nullable();
            $table->text('delivered_count')->nullable();
            $table->text('delivered_avg_pay_price')->nullable();
            $table->text('delivered_avg_discount')->nullable();
            $table->text('delivered_count_discount')->nullable();
            $table->text('delivered_purchase_sequence_day')->nullable();
            $table->text('returned_count')->nullable();
            $table->text('returned_avg_pay_price')->nullable();
            $table->text('returned_avg_discount')->nullable();
            $table->text('returned_count_discount')->nullable();
            $table->text('returned_purchase_sequence_day')->nullable();
            $table->text('canceled_count')->nullable();
            $table->text('canceled_avg_pay_price')->nullable();
            $table->text('canceled_avg_discount')->nullable();
            $table->text('canceled_count_discount')->nullable();
            $table->text('canceled_purchase_sequence_day')->nullable();
            $table->timestampsTz();
            $table->softDeletesTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_customer_order_products');
    }
};
