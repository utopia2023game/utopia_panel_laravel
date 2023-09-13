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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->text('customer_id')->nullable();
            $table->integer('submit_user_id')->default(0);
            $table->boolean('status')->default(1);
            $table->text('title')->nullable();
            $table->text('subject')->nullable();
            $table->text('text')->nullable();
            $table->text('image_path')->nullable();
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
        Schema::dropIfExists('messages');
    }
};
