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
            $table->text('order_code');
            $table->foreignId('customer_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('order_status_id')->constrained('order_statuses')->cascadeOnUpdate()->restrictOnDelete();
            $table->text('product_id');
            $table->Text('product_name');
            $table->Text('product_image')->nullable();
            $table->text('count_selected');
            $table->text('sale_price');
            $table->text('discount_price');
            $table->foreignId('address_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->Text('address_receiver_name')->nullable();
            $table->Text('address_receiver_mobile')->nullable();
            $table->Text('address_receiver_address')->nullable();
            $table->Text('address_receiver_post_code')->nullable();
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
            $table->integer('pay_id')->default(0);
            $table->Text('pay_tracking_code')->nullable();
            $table->double('pay_price')->nullable();
            $table->integer('pay_bank_id')->default(0);
            $table->Text('pay_bank_name')->nullable();
            $table->Text('pay_time_created')->nullable();
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
