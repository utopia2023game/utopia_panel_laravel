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
        Schema::create('carts', function (Blueprint $table) {
            $table->id(); 
            $table->boolean('status')->default(1);
            $table->foreignId('customer_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->integer('count_selected')->default(0);
            $table->double('sale_price')->default(0);
            $table->double('discount_price')->default(0);
            $table->timestampsTz();
            $table->softDeletesTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('carts');
    }
};
