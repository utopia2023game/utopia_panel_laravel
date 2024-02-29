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
        Schema::create('sms_customer_logons', function (Blueprint $table) {
            $table->id();
            $table->string('mobile',11);
            $table->string('code');
            $table->integer('entry_times')->default(1);
            $table->timestampsTz();
            $table->softDeletesTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::table('sms_customer_logons', function (Blueprint $table) {
            // Schema::dropIfExists('sms_customer_logons');
        // });
    }
};
