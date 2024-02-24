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

        // Schema::disableForeignKeyConstraints();
        // if (Schema::hasTable('alarm_smart_customers')) {
        //     Schema::rename('alarm_smart_customers', 'alarm_smart_offer_customers');
        // }

        // if (Schema::hasTable('alarm_smart_offer_customers')) {
        //     Schema::table('alarm_smart_offer_customers', function (Blueprint $table) {

        //         $table->boolean('setting_edit')->default(0)->after('category_id_tree');
        //         $table->text('setting_send_date')->nullable()->after('setting_edit');
        //         $table->text('setting_send_time')->nullable()->after('setting_send_date');
        //         $table->text('setting_discount')->nullable()->after('setting_send_time');
        //         $table->integer('setting_product_count')->default(1)->after('setting_discount');
        //         $table->integer('setting_category_count')->default(1)->after('setting_product_count');
        //         $table->boolean('setting_sms')->default(1)->after('setting_category_count');
        //         $table->boolean('setting_notification')->default(1)->after('setting_sms');
        //         $table->boolean('setting_email')->default(1)->after('setting_notification');

        //     });
        // }
        // Schema::enableForeignKeyConstraints();

        // if (Schema::hasTable('orders')) {
        //     Schema::table('orders', function (Blueprint $table) {
        //         $table->integer('hc_order_product_status')->default(0)->after('order_status_id');
        //     });
        // }

        // if (Schema::hasTable('history_customer_order_products')) {
        //     Schema::table('history_customer_order_products', function (Blueprint $table) {
        //         $table->renameColumn('order_times', 'all_order_times');
        //         $table->renameColumn('order_last_date', 'all_order_last_date');
        //     });
        //     Schema::table('history_customer_order_products', function (Blueprint $table) {
        //         $table->text('pending_order_times')->nullable()->after('all_purchase_sequence_day');
        //         $table->text('pending_order_last_date')->nullable()->after('pending_order_times');
        //         $table->text('delivered_order_times')->nullable()->after('pending_purchase_sequence_day');
        //         $table->text('delivered_order_last_date')->nullable()->after('delivered_order_times');
        //         $table->text('returned_order_times')->nullable()->after('delivered_purchase_sequence_day');
        //         $table->text('returned_order_last_date')->nullable()->after('returned_order_times');
        //         $table->text('canceled_order_times')->nullable()->after('returned_purchase_sequence_day');
        //         $table->text('canceled_order_last_date')->nullable()->after('canceled_order_times');
        //     });
        // }

        //     DB::table('history_customer_carts')->update([
        //         'increment_decrement' => DB::raw('count')
        //     ]);

        //     //Remove the old column:
        //     Schema::table('history_customer_carts', function(Blueprint $table)
        //     {
        //         $table->dropColumn('count');
        //     });
        // }

        // Schema::disableForeignKeyConstraints();
        // if (Schema::hasTable('history_customer_categories')) {
        //     Schema::table('history_customer_categories', function (Blueprint $table) {
        //         $table->integer('category_id')->nullable()->change();
        //     });
        //     Schema::table('history_customer_categories', function (Blueprint $table) {
        //         $table->renameColumn('result_count', 'sub_category_count');
        //     });
        //     Schema::table('history_customer_categories', function (Blueprint $table) {
        //         $table->integer('product_count')->default(0)->after('sub_category_count');
        //         $table->text('category_name')->nullable()->after('category_id');
        //         $table->text('search')->nullable()->after('category_name');
        //         $table->text('filter')->nullable()->after('search');
        //         $table->text('hashtag')->nullable()->after('filter');
        //         $table->text('sort')->nullable()->after('hashtag');
        //     });
        // }
        // Schema::enableForeignKeyConstraints();

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
        //     $table->integer('page_view_unique')->default(0)->after('page_view');
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
        // Schema::table('alarm_smart_executes', function (Blueprint $table) {
        //     $table->integer('setting_product_count')->default(1)->change();
        //     $table->integer('setting_category_count')->default(1)->change();
        // });

        // Schema::disableForeignKeyConstraints();
        // Schema::rename('alarms', 'alarm_categories');

        // Schema::dropIfExists('history_customer_devices');
        // Schema::enableForeignKeyConstraints();
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
