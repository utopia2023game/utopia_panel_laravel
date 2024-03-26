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
        // // if (Schema::hasTable('alarm_smart_customers')) {
        // //     Schema::rename('alarm_smart_customers', 'alarm_smart_offer_customers');
        // // }

        // if (Schema::hasTable('alarm_smart_categories')) {
        //     Schema::table('alarm_smart_categories', function (Blueprint $table) {
        //         $table->dropColumn('discount_tag');
        //         $table->foreignId('discount_status_id')->default(1)->after('category_count')->constrained('discount_statuses')->cascadeOnUpdate()->restrictOnDelete();
        //     });
        // }
        // if (Schema::hasTable('alarm_smart_offer_products')) {
        //     Schema::table('alarm_smart_offer_products', function (Blueprint $table) {
        //         $table->dropColumn('product_discount');
        //         $table->foreignId('product_discount_status_id')->default(1)->after('category_id')->constrained('discount_statuses')->cascadeOnUpdate()->restrictOnDelete();
        //     });
        // }
        // if (Schema::hasTable('alarm_smart_offer_categories')) {
        //     Schema::table('alarm_smart_offer_categories', function (Blueprint $table) {
        //         $table->dropColumn('category_discount');
        //         $table->foreignId('category_discount_status_id')->default(1)->after('category_id')->constrained('discount_statuses')->cascadeOnUpdate()->restrictOnDelete();
        //     });
        // }
        // if (Schema::hasTable('alarm_smart_executes')) {
        //     Schema::table('alarm_smart_executes', function (Blueprint $table) {
        //         $table->dropColumn('setting_discount');
        //         $table->foreignId('setting_discount_status_id')->default(1)->after('setting_send_time')->constrained('discount_statuses')->cascadeOnUpdate()->restrictOnDelete();
        //     });
        // }
        // if (Schema::hasTable('alarm_smart_offer_customers')) {
        //     Schema::table('alarm_smart_offer_customers', function (Blueprint $table) {
        //         $table->dropColumn('setting_discount');
        //         $table->foreignId('setting_discount_status_id')->default(1)->after('setting_send_time')->constrained('discount_statuses')->cascadeOnUpdate()->restrictOnDelete();
        //     });
        // }

        // if (Schema::hasTable('discount_statuses')) {
        //     Schema::table('discount_statuses', function (Blueprint $table) {
        //         $table->boolean('status')->default(1)->after('id');
        //     });
        // }
        // Schema::enableForeignKeyConstraints();

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
        // // if (Schema::hasTable('history_customer_categories')) {
        // //     Schema::table('history_customer_categories', function (Blueprint $table) {
        // //         $table->integer('category_id')->nullable()->change();
        // //     });
        // //     Schema::table('history_customer_categories', function (Blueprint $table) {
        // //         $table->renameColumn('result_count', 'sub_category_count');
        // //     });
        // //     Schema::table('history_customer_categories', function (Blueprint $table) {
        // //         $table->integer('product_count')->default(0)->after('sub_category_count');
        // //         $table->text('category_name')->nullable()->after('category_id');
        // //         $table->text('search')->nullable()->after('category_name');
        // //         $table->text('filter')->nullable()->after('search');
        // //         $table->text('hashtag')->nullable()->after('filter');
        // //         $table->text('sort')->nullable()->after('hashtag');
        // //     });
        // // }

        // if (Schema::hasTable('alarm_smart_offer_categories')) {
        //     Schema::table('alarm_smart_offer_categories', function (Blueprint $table) {
        //         // $table->renameColumn('category_discount_precentage', 'category_discount_percent');
        //         $table->renameColumn('category_description', 'category_descriptions');
        //     });
        // }

        // if (Schema::hasTable('alarm_smart_offer_products')) {
        //     Schema::table('alarm_smart_offer_products', function (Blueprint $table) {
        //         // $table->renameColumn('product_discount_precentage', 'product_discount_percent');
        //         $table->renameColumn('product_description', 'product_descriptions');
        //     });
        // }
        // Schema::enableForeignKeyConstraints();
        // Schema::table('products', function (Blueprint $table) {
        //     $table->renameColumn('visit', 'page_view');
        // });
        // if (Schema::hasTable('carts')) {
        //     Schema::table('carts', function (Blueprint $table) {
        //         $table->double('sale_price')->default(0)->after('count_selected');
        //         $table->double('discount_price')->default(0)->after('sale_price');
        //     });
        // }
        // if (Schema::hasTable('next_carts')) {

        //     Schema::table('next_carts', function (Blueprint $table) {
        //         $table->dropColumn('created_at');
        //         $table->dropColumn('updated_at');
        //     });
        //     Schema::table('next_carts', function (Blueprint $table) {
        //         $table->boolean('status')->default(1)->after('id');
        //         $table->foreignId('customer_id')->after('status')->constrained()->cascadeOnUpdate()->restrictOnDelete();
        //         $table->foreignId('product_id')->after('customer_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
        //         $table->integer('count_selected')->default(0)->after('product_id');
        //         $table->double('sale_price')->default(0)->after('count_selected');
        //         $table->double('discount_price')->default(0)->after('sale_price');
        //         $table->timestampsTz();
        //         $table->softDeletesTz();
        //     });
        // }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::disableForeignKeyConstraints();

        // if (Schema::hasTable('carts')) {
        //     Schema::table('carts', function (Blueprint $table) {
        //         $table->integer('count_selected')->nullable(false)->default(0)->change();
        //     });
        // }

        // if (Schema::hasTable('alarm_smart_offer_categories')) {
        //     Schema::table('alarm_smart_offer_categories', function (Blueprint $table) {

        //         $table->double('category_discount_precentage')->default(0.0)->change();
        //     });
        // }

        // if (Schema::hasTable('alarm_smart_offer_products')) {
        //     // Schema::table('alarm_smart_offer_products', function (Blueprint $table) {
        //     //     // $table->dropForeign('alarm_smart_offer_products_customer_id_foreign');
        //     //     $table->dropColumn('customer_id');
        //     // });
        //     Schema::table('alarm_smart_offer_products', function (Blueprint $table) {
        //         $table->foreignId('customer_id')->after('edit')->constrained()->cascadeOnUpdate()->restrictOnDelete();
        //     });
        // }

        // if (Schema::hasTable('alarm_smart_offer_categories')) {
        //     // Schema::table('alarm_smart_offer_categories', function (Blueprint $table) {
        //     //     $table->dropForeign('alarm_smart_offer_categories_customer_id_foreign');
        //     //     $table->dropColumn('customer_id');
        //     // });
        //     Schema::table('alarm_smart_offer_categories', function (Blueprint $table) {
        //         $table->foreignId('customer_id')->after('edit')->constrained()->cascadeOnUpdate()->restrictOnDelete();
        //     });
        // }

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