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
            $table->text('categories_id')->nullable(true);
            $table->text('title')->nullable(true);
            $table->text('html')->nullable(true);
            $table->text('ribbon')->nullable(true);
            $table->integer('weight')->nullable(true);
            $table->integer('width')->nullable(true);
            $table->integer('height')->nullable(true);
            $table->integer('length')->nullable(true);
            $table->integer('stack_status')->default(0);
            $table->integer('stack_count')->nullable(true);
            $table->integer('stack_limit')->nullable(true);
            $table->text('barcode')->nullable(true);
            $table->text('product_code')->nullable(true);
            $table->double('sale_price')->nullable(true);
            $table->double('purchase_price')->nullable(true);
            $table->boolean('confirm_discount')->default(0);
            $table->double('discount_percent')->nullable(true);
            $table->double('discount_manual')->nullable(true);
            $table->boolean('confirm_discount_time')->default(0);
            $table->text('discount_time_from')->nullable(true);
            $table->text('discount_time_until')->nullable(true);
            $table->double('safe_discount_percent')->nullable(true);
            $table->double('special_discount_percent')->nullable(true);
            $table->double('exceptional_discount_percent')->nullable(true);
            $table->integer('visit')->default(0);
            $table->timestampsTz();
            $table->softDeletesTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
