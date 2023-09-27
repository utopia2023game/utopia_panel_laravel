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
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->boolean('status')->default(1);
            $table->boolean('priority')->default(0);
            $table->text('name');
            $table->text('description')->nullable();
            $table->integer('price')->default(0);
            $table->integer('delay_day_start')->default(0);
            $table->integer('delay_day_from')->default(0);
            $table->integer('delay_day_until')->default(0);
            $table->text('delay_time_from')->nullable();
            $table->text('delay_time_until')->nullable();
            $table->timestampsTz();
            $table->softDeletesTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
