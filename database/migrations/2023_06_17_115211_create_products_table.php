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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict')->onUpdate('cascade');
            $table->foreignId('user_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->text('categories_id')->nullable();
            $table->text('attributes_id')->nullable();
            $table->text('specification_id')->nullable();
            $table->text('hashtag_id')->nullable();
            $table->text('title')->nullable();
            $table->text('model')->nullable();
            $table->text('html')->nullable();
            $table->text('ribbon')->nullable();
            $table->double('weight')->default(0.0);
            $table->double('width')->default(0.0);
            $table->double('height')->default(0.0);
            $table->double('length')->default(0.0);
            $table->integer('stack_status')->default(0);
            $table->integer('stack_count')->nullable();
            $table->integer('stack_limit')->nullable();
            $table->text('barcode')->nullable();
            $table->text('product_code')->nullable();
            $table->double('sale_price')->nullable();
            $table->double('purchase_price')->nullable();
            $table->boolean('confirm_discount')->default(0);
            $table->double('discount_percent')->nullable();
            $table->double('discount_manual')->nullable();
            $table->double('discount_price')->default(0);
            $table->text('discount_time_from')->nullable();
            $table->text('discount_time_until')->nullable();
            $table->double('safe_discount_percent')->nullable();
            $table->double('special_discount_percent')->nullable();
            $table->double('exceptional_discount_percent')->nullable();
            $table->integer('page_view')->default(0);
            $table->integer('page_view_unique')->default(0);
            $table->integer('page_view_avg_time')->default(0);
            $table->integer('pay')->default(0);
            $table->integer('process')->default(0);
            $table->integer('delivery')->default(0);
            $table->integer('cancel')->default(0);
            $table->integer('return')->default(0);
            $table->timestampsTz();
            $table->softDeletesTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('products');
    }
};
