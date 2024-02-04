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
        Schema::create('history_customer_analytics_customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->integer('score')->default(0);
            $table->text('product_id')->nullable();
            $table->text('product_avg_time_view')->nullable();
            $table->text('product_count_view')->nullable();
            $table->text('category_id')->nullable();
            $table->text('category_avg_time_view')->nullable();
            $table->text('category_count_view')->nullable();
            $table->text('category_search_count')->nullable();
            $table->text('category_filter_count')->nullable();
            $table->text('category_hashtag_count')->nullable();
            $table->text('category_sort_count')->nullable();
            $table->text('product_all_order_times')->nullable();
            $table->text('product_all_order_product_discount_times')->nullable();
            $table->text('product_all_order_product_free_delivery_times')->nullable();
            $table->text('product_all_order_product_count')->nullable();
            $table->text('product_all_order_product_discount_count')->nullable();
            $table->text('product_all_order_product_free_delivery_count')->nullable();
            $table->text('product_delivered_order_times')->nullable();
            $table->text('product_delivered_order_product_discount_times')->nullable();
            $table->text('product_delivered_order_product_free_delivery_times')->nullable();
            $table->text('product_delivered_order_product_count')->nullable();
            $table->text('product_delivered_order_product_discount_count')->nullable();
            $table->text('product_delivered_order_product_free_delivery_count')->nullable();
            $table->text('product_pending_order_times')->nullable();
            $table->text('product_pending_order_product_discount_times')->nullable();
            $table->text('product_pending_order_product_free_delivery_times')->nullable();
            $table->text('product_pending_order_product_count')->nullable();
            $table->text('product_pending_order_product_discount_count')->nullable();
            $table->text('product_pending_order_product_free_delivery_count')->nullable();
            $table->text('product_like')->nullable();
            $table->text('product_share_count')->nullable();
            $table->text('product_cart_times')->nullable();
            $table->text('product_cart_increment_decrement')->nullable();
            $table->text('product_next_cart_times')->nullable();
            $table->text('product_next_cart_increment_decrement')->nullable();
            $table->text('hc_view_id')->nullable();
            $table->text('hc_order_product_id')->nullable();
            $table->text('hc_like_id')->nullable();
            $table->text('hc_share_id')->nullable();
            $table->text('hc_cart_id')->nullable();
            $table->text('hc_next_cart_id')->nullable();
            $table->timestampsTz();
            $table->softDeletesTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_customer_analytics_customers');
    }
};
