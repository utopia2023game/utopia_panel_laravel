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
        Schema::create('analytics_customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->integer('score')->default(0);
            $table->integer('product_id')->default(0);
            $table->integer('product_avg_time_view')->default(0);
            $table->integer('product_count_view')->default(0);
            $table->integer('category_id')->default(0);
            $table->integer('category_avg_time_view')->default(0);
            $table->integer('category_count_view')->default(0);
            $table->integer('category_search_count')->default(0);
            $table->integer('category_filter_count')->default(0);
            $table->integer('category_hashtag_count')->default(0);
            $table->integer('category_sort_count')->default(0);
            $table->integer('product_all_order_times')->default(0);
            $table->integer('product_all_order_product_discount_times')->default(0);
            $table->integer('product_all_order_product_free_delivery_times')->default(0);
            $table->integer('product_all_order_product_count')->default(0);
            $table->integer('product_all_order_product_discount_count')->default(0);
            $table->integer('product_all_order_product_free_delivery_count')->default(0);
            $table->integer('product_delivered_order_times')->default(0);
            $table->integer('product_delivered_order_product_discount_times')->default(0);
            $table->integer('product_delivered_order_product_free_delivery_times')->default(0);
            $table->integer('product_delivered_order_product_count')->default(0);
            $table->integer('product_delivered_order_product_discount_count')->default(0);
            $table->integer('product_delivered_order_product_free_delivery_count')->default(0);
            $table->integer('product_pending_order_times')->default(0);
            $table->integer('product_pending_order_product_discount_times')->default(0);
            $table->integer('product_pending_order_product_free_delivery_times')->default(0);
            $table->integer('product_pending_order_product_count')->default(0);
            $table->integer('product_pending_order_product_discount_count')->default(0);
            $table->integer('product_pending_order_product_free_delivery_count')->default(0);
            $table->integer('product_like')->default(0);
            $table->integer('product_share_count')->default(0);
            $table->integer('product_cart_times')->default(0);
            $table->integer('product_cart_increment_decrement')->default(0);
            $table->integer('product_next_cart_times')->default(0);
            $table->integer('product_next_cart_increment_decrement')->default(0);
            $table->text('hc_view_id')->nullable();
            $table->text('hc_category_id')->nullable();
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
        // Schema::dropIfExists('analytics_customers');
    }
};
