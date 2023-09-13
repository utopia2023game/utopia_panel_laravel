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
        if (Schema::hasTable('customers')) {
            if (!Schema::hasColumn('customers', 'pic_profile')) {
                Schema::table('customers', function (Blueprint $table) {
                    $table->Text('pic_profile')->nullable()->after('email_verified_at');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('customers')) {
            if (Schema::hasColumn('customers', 'pic_profile')) {
                Schema::table('customers', function (Blueprint $table) {
                    $table->dropColumn('pic_profile');
                });
            }
        }
    }
};