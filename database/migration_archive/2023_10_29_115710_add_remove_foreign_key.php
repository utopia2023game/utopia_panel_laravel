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
        if (Schema::hasTable('orders')) {
            if (!Schema::hasColumn('orders', 'order_status_id')) {
                Schema::table('orders', function (Blueprint $table) {
                    $table->foreignId('order_status_id')->default(1)->after('customer_id')->constrained('order_statuses')->cascadeOnUpdate()->restrictOnDelete();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // try {
        //     Schema::table('orders', function (Blueprint $table) {
        //             $table->dropForeign('orders_order_status_id_foreign');
        //             $table->dropColumn('order_status_id');
        //     });
        // } catch (\Throwable $th) {
        //     throw $th;
        // }
        
    }
};