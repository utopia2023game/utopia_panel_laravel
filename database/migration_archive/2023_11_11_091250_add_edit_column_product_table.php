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
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                $table->renameColumn('visit', 'page_view');
            });
            Schema::table('products', function (Blueprint $table) {
                $table->Text('attributes_id')->nullable()->after('categories_id');
                $table->Text('specification_id')->nullable()->after('attributes_id');
                $table->Text('hashtag_id')->nullable()->after('specification_id');
                $table->Text('model')->nullable()->after('title');
                $table->double('weight')->change();
                $table->double('width')->change();
                $table->double('height')->change();
                $table->double('length')->change();
                $table->integer('page_view_uniqe')->default(0)->after('page_view');
                $table->integer('page_view_avg_time')->default(0)->after('page_view_uniqe');
                $table->integer('pay')->default(0)->after('page_view_avg_time');
                $table->integer('process')->default(0)->after('pay');
                $table->integer('delivery')->default(0)->after('process');
                $table->integer('cancel')->default(0)->after('delivery');
                $table->integer('return')->default(0)->after('cancel');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        
    }
};