<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn('products', 'confirm_discount_time')) {
            Schema::table('products', function (Blueprint $table) {
                $table->double('confirm_discount_time')->default(0)->change();
            });
            Schema::table('products', function (Blueprint $table) {
                $table->renameColumn('confirm_discount_time', 'discount_price');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // if (Schema::hasColumn('products', 'discount_price')) {
        // Schema::table('products', function (Blueprint $table) {
        //     $table->boolean('discount_price')->default(0)->change();
        // });
        // Schema::table('products', function (Blueprint $table) {
        //     $table->renameColumn('discount_price', 'confirm_discount_time');
        // });
        // }
    }
};