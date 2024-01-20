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
        Schema::create('history_customer_devices', function (Blueprint $table) {
            $table->id();
            $table->text('unique_id')->nullable();
            $table->foreignId('customer_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->text('device')->nullable();
            $table->text('physical_width_device')->nullable();
            $table->text('physical_height_device')->nullable();
            $table->text('platform')->nullable();
            $table->text('ip_address')->nullable();
            $table->text('ip_type')->nullable();
            $table->text('isp_name')->nullable();
            $table->text('isp_domain')->nullable();
            $table->text('location_continent_code')->nullable();
            $table->text('location_continent_name')->nullable();
            $table->text('location_country_code')->nullable();
            $table->text('location_country_name')->nullable();
            $table->text('location_country_capital')->nullable();
            $table->text('location_country_calling_code')->nullable();
            $table->text('location_country_flag_circle')->nullable();
            $table->text('location_country_flag_rectangle')->nullable();
            $table->text('location_region_code')->nullable();
            $table->text('location_region_name')->nullable();
            $table->text('location_city')->nullable();
            $table->text('location_latitude')->nullable();
            $table->text('location_longitude')->nullable();
            $table->text('location_latitude_device')->nullable();
            $table->text('location_longitude_device')->nullable();
            $table->text('location_language_code')->nullable();
            $table->text('location_language_name')->nullable();
            $table->text('location_language_native')->nullable();
            $table->text('time_zone')->nullable();
            $table->text('device_browser')->nullable();
            $table->text('device_os_name')->nullable();
            $table->text('device_os_type')->nullable();
            $table->text('execute_time')->nullable();
            $table->text('status')->nullable();
            $table->text('message')->nullable();
            $table->timestampsTz();
            $table->softDeletesTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_customer_devices');
    }
};
