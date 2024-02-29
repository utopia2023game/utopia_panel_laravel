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
        Schema::create('alarm_smart_offer_categories', function (Blueprint $table) {
            $table->id();
            $table->boolean('edit')->default(0);
            $table->foreignId('customer_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->text('category_discount')->nullable();
            $table->text('category_discount_precentage')->nullable();
            $table->text('category_discription')->nullable();
            $table->timestampsTz();
            $table->softDeletesTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alarm_smart_offer_categories');
    }
};
