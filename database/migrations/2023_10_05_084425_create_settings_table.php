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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->text('h_c_device_record_count')->nullable();
            $table->text('h_c_user_record_count')->nullable();
            $table->text('h_c_hashtag_record_count')->nullable();
            $table->text('h_c_share_record_count')->nullable();
            $table->text('h_c_like_record_count')->nullable();
            $table->text('h_c_route_record_count')->nullable();
            $table->text('h_c_category_record_count')->nullable();
            $table->text('h_c_view_record_count')->nullable();
            $table->text('h_c_sort_record_count')->nullable();
            $table->text('h_c_search_record_count')->nullable();
            $table->text('h_c_filter_record_count')->nullable();
            $table->text('h_c_order_record_count')->nullable();
            $table->text('h_c_cart_record_count')->nullable();
            $table->text('h_c_next_cart_record_count')->nullable();
            $table->timestampsTz();
            $table->softDeletesTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
