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
        Schema::create('original_financial_ranks', function (Blueprint $table) {
            $table->id();
            $table->integer('product_price_low_price')->default(0);
            $table->integer('product_price_mid_low_price')->default(0);
            $table->integer('product_price_mid_high_price')->default(0);
            $table->integer('product_price_high_price')->default(0);
            $table->integer('avg_purchase_low_price')->default(0);
            $table->integer('avg_purchase_mid_low_price')->default(0);
            $table->integer('avg_purchase_mid_high_price')->default(0);
            $table->integer('avg_purchase_high_price')->default(0);
            $table->integer('total_purchase_low_price')->default(0);
            $table->integer('total_purchase_mid_low_price')->default(0);
            $table->integer('total_purchase_mid_high_price')->default(0);
            $table->integer('total_purchase_high_price')->default(0);
            $table->timestampsTz();
            $table->softDeletesTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('original_financial_ranks');
    }
};
