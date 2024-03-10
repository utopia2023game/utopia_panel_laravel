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
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->integer('customer_id')->default(0);
            $table->integer('category_id')->default(0);
            $table->integer('product_id')->default(0);
            $table->boolean('status')->default(0);
            $table->boolean('edit')->default(0);
            $table->integer('priority')->default(0);
            $table->text('name')->nullable();
            $table->text('code')->nullable(false);
            $table->boolean('discount_type')->default(0);  // 0 percent and 1 manual
            $table->double('discount_precent')->default(0);
            $table->double('discount_manual')->default(0);
            $table->boolean('limit_maximum_discount_status')->default(0);
            $table->double('limit_maximum_discount_price')->default(0);
            $table->boolean('free_delivery_status')->default(0);
            $table->boolean('limit_minimum_order_status')->default(0);
            $table->double('limit_minimum_order_price')->default(0);
            $table->boolean('unlimited_use_discount_status')->default(0);
            $table->integer('unlimited_use_discount_count')->default(0);
            $table->integer('unlimited_use_discount_remained')->default(0);
            $table->boolean('first_order_status')->default(0);
            $table->boolean('unlimited_use_each_customer_status')->default(0);
            $table->integer('unlimited_use_each_customer_count')->default(0);
            $table->Text('time_start')->nullable();
            $table->Text('time_end')->nullable();
            $table->timestampsTz();
            $table->softDeletesTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};
