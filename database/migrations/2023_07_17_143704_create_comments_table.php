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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();  // customer_id 
            $table->integer('submit_user_id')->default(0);
            $table->boolean('status')->default(1);
            $table->text('name')->nullable();
            $table->text('text')->nullable();
            $table->text('response')->nullable();
            $table->double('rate')->default(0);
            $table->integer('like')->default(0);
            $table->integer('dislike')->default(0);
            $table->timestampsTz();
            $table->softDeletesTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('comments');
    }
};
