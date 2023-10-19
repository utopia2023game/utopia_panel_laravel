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

        // if (Schema::hasTable('messages')) {
        //     Schema::table('messages', function (Blueprint $table) {
        //         $table->renameColumn('visit', 'page_view');
        //     });
        // }
        // if (Schema::hasTable('products')) {
        // Schema::table('products', function (Blueprint $table) {
        //     $table->renameColumn('visit', 'page_view');
        // });
        // Schema::table('products', function (Blueprint $table) {
        //     $table->Text('attributes_id')->nullable()->after('categories_id');
        //     $table->Text('specification_id')->nullable()->after('attributes_id');
        //     $table->Text('hashtag_id')->nullable()->after('specification_id');
        //     $table->Text('model')->nullable()->after('title');
        //     $table->double('weight')->change();
        //     $table->double('width')->change();
        //     $table->double('height')->change();
        //     $table->double('length')->change();
        //     $table->integer('page_view_uniqe')->default(0)->after('page_view');
        //     $table->integer('page_view_avg_time')->default(0)->after('page_view_uniqe');
        //     $table->integer('pay')->default(0)->after('page_view_avg_time');
        //     $table->integer('process')->default(0)->after('pay');
        //     $table->integer('delivery')->default(0)->after('process');
        //     $table->integer('cancel')->default(0)->after('delivery');
        //     $table->integer('return')->default(0)->after('cancel');
        // });
        // }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
};
// try {
//             Schema::table('history_customer_carts', function (Blueprint $table) {
//                 $table->dropForeign('history_customer_carts_product_id_foreign');
//                 $table->dropColumn('product_id');
//             });

//             Schema::table('history_customer_next_carts', function (Blueprint $table) {
//                 $table->dropForeign('history_customer_next_carts_product_id_foreign');
//                 $table->dropColumn('product_id');
//             });
//         } catch (\Throwable $th) {
//             throw $th;
//         }
// if (Schema::hasTable('history_customer_carts')) {
//     Schema::table('history_customer_carts', function (Blueprint $table) {
//         $table->foreignId('product_id')->after('tag')->constrained()->cascadeOnUpdate()->restrictOnDelete();
//     });
// }
// if (Schema::hasTable('history_customer_next_carts')) {
//     Schema::table('history_customer_next_carts', function (Blueprint $table) {
//         $table->foreignId('product_id')->after('tag')->constrained()->cascadeOnUpdate()->restrictOnDelete();
//     });
// }



// if (Schema::hasTable('history_customer_carts')) {
//     Schema::table('history_customer_carts', function (Blueprint $table) {
//         $table->text('status')->nullable()->after('execute_time');
//         $table->text('message')->nullable()->after('status');
//     });
// }
// if (Schema::hasTable('history_customer_categories')) {
//     Schema::table('history_customer_categories', function (Blueprint $table) {
//         $table->integer('result_count')->default(0)->after('category_id');
//         $table->text('status')->nullable()->after('time_end');
//         $table->text('message')->nullable()->after('status');
//     });
// }


// if (Schema::hasTable('history_customer_devices')) {
//     Schema::table('history_customer_devices', function (Blueprint $table) {
//         $table->text('execute_time')->nullable()->after('ip_address');
//     });
// }
// if (Schema::hasTable('history_customer_filters')) {
//             Schema::table('history_customer_filters', function (Blueprint $table) {
//                 $table->integer('result_count')->default(0)->after('tag');
//                 $table->text('status')->nullable()->after('time_end');
//                 $table->text('message')->nullable()->after('status');
//             });
//         }
//         if (Schema::hasTable('history_customer_hash_tags')) {
//             Schema::table('history_customer_hash_tags', function (Blueprint $table) {
//                 $table->integer('result_count')->default(0)->after('tag');
//                 $table->text('status')->nullable()->after('time_end');
//                 $table->text('message')->nullable()->after('status');
//             });
//         }
//         if (Schema::hasTable('history_customer_likes')) {
//             Schema::table('history_customer_likes', function (Blueprint $table) {
//                 $table->text('status')->nullable()->after('execute_time');
//                 $table->text('message')->nullable()->after('status');
//             });
//         }
//         if (Schema::hasTable('history_customer_next_carts')) {
//             Schema::table('history_customer_next_carts', function (Blueprint $table) {
//                 $table->text('status')->nullable()->after('execute_time');
//                 $table->text('message')->nullable()->after('status');
//             });
//         }
//         if (Schema::hasTable('history_customer_orders')) {
//             Schema::table('history_customer_orders', function (Blueprint $table) {
//                 $table->text('status')->nullable()->after('execute_time');
//                 $table->text('message')->nullable()->after('status');
//             });
//         }
//         if (Schema::hasTable('history_customer_routes')) {
//             Schema::table('history_customer_routes', function (Blueprint $table) {
//                 $table->text('status')->nullable()->after('time_end');
//                 $table->text('message')->nullable()->after('status');
//             });
//         }
//         if (Schema::hasTable('history_customer_searches')) {
//             Schema::table('history_customer_searches', function (Blueprint $table) {
//                 $table->integer('result_count')->default(0)->after('tag');
//                 $table->text('status')->nullable()->after('time_end');
//                 $table->text('message')->nullable()->after('status');
//             });
//         }
//         if (Schema::hasTable('history_customer_settings')) {
//             Schema::table('history_customer_settings', function (Blueprint $table) {
//                 $table->text('status')->nullable()->after('execute_time');
//                 $table->text('message')->nullable()->after('status');
//             });
//         }
//         if (Schema::hasTable('history_customer_shares')) {
//             Schema::table('history_customer_shares', function (Blueprint $table) {
//                 $table->text('status')->nullable()->after('execute_time');
//                 $table->text('message')->nullable()->after('status');
//             });
//         }
//         if (Schema::hasTable('history_customer_sorts')) {
//             Schema::table('history_customer_sorts', function (Blueprint $table) {
//                 $table->integer('result_count')->default(0)->after('tag');
//                 $table->text('status')->nullable()->after('time_end');
//                 $table->text('message')->nullable()->after('status');
//             });
//         }
//         if (Schema::hasTable('history_customer_users')) {
//             Schema::table('history_customer_users', function (Blueprint $table) {
//                 $table->text('status')->nullable()->after('execute_time');
//                 $table->text('message')->nullable()->after('status');
//             });
//         }
//         if (Schema::hasTable('history_customer_views')) {
//             Schema::table('history_customer_views', function (Blueprint $table) {
//                 $table->text('status')->nullable()->after('time_end');
//                 $table->text('message')->nullable()->after('status');
//             });
//         }