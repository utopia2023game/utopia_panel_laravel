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
            $table->text('product_id')->nullable();
            $table->integer('status')->default(0);
            $table->text('category_id')->nullable();
            $table->text('category_avg_time')->nullable();
            $table->text('category_count_view')->nullable();
            $table->text('category_search')->nullable();
            $table->text('category_filter')->nullable();
            $table->text('category_hashtag')->nullable();
            $table->text('category_sort')->nullable();
            $table->text('product_avg_time_view')->nullable();
            $table->text('product_count_view')->nullable();
            $table->text('product_order_count')->nullable();
            $table->text('product_like_count')->nullable();
            $table->text('product_share_count')->nullable();
            $table->text('product_cart_increment')->nullable();
            $table->text('product_cart_decrement')->nullable();
            $table->text('product_cart_delete')->nullable();
            $table->text('product_next_cart_increment')->nullable();
            $table->text('product_next_cart_decrement')->nullable();
            $table->text('product_next_cart_delete')->nullable();
            $table->text('hc_view_id')->nullable();
            $table->text('order_id')->nullable();
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
