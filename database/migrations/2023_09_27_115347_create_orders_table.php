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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->text('status');
            $table->text('product_id');
            $table->text('count_selected');
            $table->text('sale_price');
            $table->text('discount_price');
            $table->foreignId('address_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('delivery_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->text('delivery_type_name');
            $table->text('delivery_type_price');
            $table->text('delivery_date');
            $table->text('delivery_time');
            $table->integer('all_count_products');
            $table->double('all_sale_price_products');
            $table->double('all_discount_price_products');
            $table->double('all_price_all');
            $table->text('all_discount_code');
            $table->double('all_discount_price');
            $table->text('all_gift_card_code');
            $table->double('all_gift_card_price');
            $table->boolean('all_wallet_confirm')->default(0);
            $table->double('all_wallet_price');
            $table->double('all_wallet_price_use');
            $table->timestampsTz();
            $table->softDeletesTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
