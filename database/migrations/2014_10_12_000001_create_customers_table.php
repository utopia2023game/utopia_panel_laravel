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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->boolean('status')->default(0);
            $table->string('name')->nullable();
            $table->string('family')->nullable();
            $table->string('mobile' , 11)->unique();
            $table->timestamp('mobile_verified_at')->nullable();
            $table->string('email')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('pic_profile')->nullable();
            $table->string('password');
            $table->string('birth');
            $table->string('gender');
            $table->string('weight');
            $table->string('height');
            $table->string('rank');
            $table->rememberToken();
            $table->timestampsTz();
            $table->softDeletesTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('customers');
    }
};
