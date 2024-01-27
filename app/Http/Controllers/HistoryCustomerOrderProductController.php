<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Models\HistoryCustomerOrderProduct;

class HistoryCustomerOrderProductController extends Controller
{
    public function set_hc_order_product_table_refresh_all()
    {
        $input = Request()->all();

        Helper::DBConnection(env('SERVER_STATUS', '') . 'utopia_store_' . $input['idb']);

        $customer = Customer::select('id')->get();



        for ($i = 0; $i < count($customer); $i++) {

            $order = Order::where('customer_id', $customer[$i]->id)->get();
            // echo 'customer_id '. $customer[$i]->id .' order ' .($order) ."\n";
            for ($j = 0; $j < count($order); $j++) {

                $product_id = json_decode($order[$j]->product_id);
                // echo ' product_id string ' . ($order[$j]->product_id) . "\n";
                for ($k = 0; $k < count($product_id); $k++) {
                    // echo ' product_id [$k] ' . ($product_id[$k]) . "\n";

                    if (HistoryCustomerOrderProduct::where('customer_id', $customer[$i]->id)->where('product_id', $product_id[$k])->exists()) {
                        $orderProduct = HistoryCustomerOrderProduct::where('customer_id', $customer[$i]->id)->where('product_id', $product_id[$k])->first();

                        $key = array_search($product_id[$k], $product_id);

                        $order_status_id = $order[$j]->order_status_id;
                        if ($order_status_id <= 5) {
                            $order_status = 'pending';
                        } else if ($order_status_id == 8) {
                            $order_status = 'canceled';
                        } else if ($order_status_id == 14) {
                            $order_status = 'returned';
                        } else if ($order_status_id == 6 || $order_status_id == 7) {
                            $order_status = 'delivered';
                        }

                        $count_selected_array = json_decode($order[$j]->count_selected);
                        $count_selected = $count_selected_array[$key] ?? 1;
                        $sale_price_array = json_decode($order[$j]->sale_price);
                        $sale_price = $sale_price_array[$key] ?? 0;
                        $discount_price_array = json_decode($order[$j]->discount_price);
                        $discount_price = $discount_price_array[$key] ?? 0;
                        $count_discount = $discount_price[$key] ?? 0;
                        $count_discount = $count_discount > 0 ? 1 : 0;


                        $a = array();
                        $a['customer_id'] = $customer[$i]->id;
                        $a['product_id'] = $product_id[$k];

                        $a['all_order_times'] = intval($orderProduct->all_order_times) + 1;
                        $a['all_order_last_date'] = $order[$j]->created_at;
                        $a['all_count'] = intval($orderProduct->all_count) + $count_selected;
                        $a['all_avg_pay_price'] = (intval($orderProduct->all_avg_pay_price) + $sale_price) / 2;
                        $a['all_avg_discount'] = (intval($orderProduct->all_avg_discount) + $discount_price) / 2;
                        $a['all_count_discount'] = $orderProduct->all_count_discount + $count_discount;
                        // $a['all_purchase_sequence_day'] = $orderProduct->all_purchase_sequence_day;

                        $a['pending_order_times'] = intval($orderProduct->pending_order_times) + ($order_status == 'pending' ? 1 : 0);
                        $a['pending_order_last_date'] = $order_status == 'pending' ? $order[$j]->created_at : null;
                        $a['pending_count'] = $orderProduct->pending_count + ($order_status == 'pending' ? $count_selected : 0);
                        $a['pending_avg_pay_price'] = $order_status == 'pending' ? (intval($orderProduct->pending_avg_pay_price) + $sale_price) / 2 : $orderProduct->pending_avg_pay_price;
                        $a['pending_avg_discount'] = $order_status == 'pending' ? (intval($orderProduct->pending_avg_discount) + $discount_price) / 2 : $orderProduct->pending_avg_discount;
                        $a['pending_count_discount'] = $orderProduct->pending_count_discount + ($order_status == 'pending' ? $count_discount : 0);
                        // $a['pending_purchase_sequence_day'] = $orderProduct->pending_purchase_sequence_day;

                        $a['delivered_order_times'] = intval($orderProduct->delivered_order_times) + ($order_status == 'delivered' ? 1 : 0);
                        $a['delivered_order_last_date'] = $order_status == 'delivered' ? $order[$j]->created_at : null;
                        $a['delivered_count'] = $orderProduct->delivered_count + ($order_status == 'delivered' ? $count_selected : 0);
                        $a['delivered_avg_pay_price'] = $order_status == 'delivered' ? (intval($orderProduct->delivered_avg_pay_price) + $sale_price) / 2 : $orderProduct->delivered_avg_pay_price;
                        $a['delivered_avg_discount'] = $order_status == 'delivered' ? (intval($orderProduct->delivered_avg_discount) + $discount_price) / 2 : $orderProduct->delivered_avg_discount;
                        $a['delivered_count_discount'] = $orderProduct->delivered_count_discount + ($order_status == 'delivered' ? $count_discount : 0);
                        // $a['delivered_purchase_sequence_day'] = $orderProduct->delivered_purchase_sequence_day;

                        $a['returned_order_times'] = intval($orderProduct->returned_order_times) + ($order_status == 'returned' ? 1 : 0);
                        $a['returned_order_last_date'] = $order_status == 'returned' ? $order[$j]->created_at : null;
                        $a['returned_count'] = $orderProduct->returned_count + ($order_status == 'returned' ? $count_selected : 0);
                        $a['returned_avg_pay_price'] = $order_status == 'returned' ? (intval($orderProduct->returned_avg_pay_price) + $sale_price) / 2 : $orderProduct->returned_avg_pay_price;
                        $a['returned_avg_discount'] = $order_status == 'returned' ? (intval($orderProduct->returned_avg_discount) + $discount_price) / 2 : $orderProduct->returned_avg_discount;
                        $a['returned_count_discount'] = $orderProduct->returned_count_discount + ($order_status == 'returned' ? $count_discount : 0);
                        // $a['returned_purchase_sequence_day'] = $orderProduct->returned_purchase_sequence_day;

                        $a['canceled_order_times'] = intval($orderProduct->canceled_order_times) + ($order_status == 'canceled' ? 1 : 0);
                        $a['canceled_order_last_date'] = $order_status == 'canceled' ? $order[$j]->created_at : null;
                        $a['canceled_count'] = $orderProduct->canceled_count + ($order_status == 'canceled' ? $count_selected : 0);
                        $a['canceled_avg_pay_price'] = $order_status == 'canceled' ? (intval($orderProduct->canceled_avg_pay_price) + $sale_price) / 2 : $orderProduct->canceled_avg_pay_price;
                        $a['canceled_avg_discount'] = $order_status == 'canceled' ? (intval($orderProduct->canceled_avg_discount) + $discount_price) / 2 : $orderProduct->canceled_avg_discount;
                        $a['canceled_count_discount'] = $orderProduct->canceled_count_discount + ($order_status == 'canceled' ? $count_discount : 0);
                        // $a['cancel_purchase_sequence_day'] = $orderProduct->cancel_purchase_sequence_day;

                        HistoryCustomerOrderProduct::where('customer_id', $customer[$i]->id)->where('product_id', $product_id[$k])->update($a);
                    } else {
                        $key = array_search($product_id[$k], $product_id);

                        $order_status_id = $order[$j]->order_status_id;
                        if ($order_status_id <= 5) {
                            $order_status = 'pending';
                        } else if ($order_status_id == 8) {
                            $order_status = 'canceled';
                        } else if ($order_status_id == 14) {
                            $order_status = 'returned';
                        } else if ($order_status_id == 6 || $order_status_id == 7) {
                            $order_status = 'delivered';
                        }

                        $count_selected_array = json_decode($order[$j]->count_selected);
                        $count_selected = $count_selected_array[$key] ?? 1;
                        $sale_price_array = json_decode($order[$j]->sale_price);
                        $sale_price = $sale_price_array[$key] ?? 0;
                        $discount_price_array = json_decode($order[$j]->discount_price);
                        $discount_price = $discount_price_array[$key] ?? 0;
                        $count_discount = $discount_price[$key] ?? 0;
                        $count_discount = $count_discount > 0 ? 1 : 0;


                        $a = array();
                        $a['customer_id'] = $customer[$i]->id;
                        $a['product_id'] = $product_id[$k];

                        $a['all_order_times'] = 1;
                        $a['all_order_last_date'] = $order[$j]->created_at;
                        $a['all_count'] = $count_selected;
                        $a['all_avg_pay_price'] = $sale_price;
                        $a['all_avg_discount'] = $discount_price;
                        $a['all_count_discount'] = $count_discount;
                        // $a['all_purchase_sequence_day'] = $orderProduct->all_purchase_sequence_day;

                        $a['pending_order_times'] = $order_status == 'pending' ? 1 : 0;
                        $a['pending_order_last_date'] = $order_status == 'pending' ? $order[$j]->created_at : null;
                        $a['pending_count'] = $order_status == 'pending' ? $count_selected : 0;
                        $a['pending_avg_pay_price'] = $order_status == 'pending' ? $sale_price : 0;
                        $a['pending_avg_discount'] = $order_status == 'pending' ? $discount_price : 0;
                        $a['pending_count_discount'] = $order_status == 'pending' ? $count_discount : 0;
                        // $a['pending_purchase_sequence_day'] = $orderProduct->pending_purchase_sequence_day;

                        $a['delivered_order_times'] = $order_status == 'delivered' ? 1 : 0;
                        $a['delivered_order_last_date'] = $order_status == 'delivered' ? $order[$j]->created_at : null;
                        $a['delivered_count'] = $order_status == 'delivered' ? $count_selected : 0;
                        $a['delivered_avg_pay_price'] = $order_status == 'delivered' ? $sale_price : 0;
                        $a['delivered_avg_discount'] = $order_status == 'delivered' ? $discount_price : 0;
                        $a['delivered_count_discount'] = $order_status == 'delivered' ? $count_discount : 0;
                        // $a['delivered_purchase_sequence_day'] = $orderProduct->delivered_purchase_sequence_day;

                        $a['returned_order_times'] = $order_status == 'returned' ? 1 : 0;
                        $a['returned_order_last_date'] = $order_status == 'returned' ? $order[$j]->created_at : null;
                        $a['returned_count'] = $order_status == 'returned' ? $count_selected : 0;
                        $a['returned_avg_pay_price'] = $order_status == 'returned' ? $sale_price : 0;
                        $a['returned_avg_discount'] = $order_status == 'returned' ? $discount_price : 0;
                        $a['returned_count_discount'] = $order_status == 'returned' ? $count_discount : 0;
                        // $a['returned_purchase_sequence_day'] = $orderProduct->returned_purchase_sequence_day;

                        $a['canceled_order_times'] = $order_status == 'canceled' ? 1 : 0;
                        $a['canceled_order_last_date'] = $order_status == 'canceled' ? $order[$j]->created_at : null;
                        $a['canceled_count'] = $order_status == 'canceled' ? $count_selected : 0;
                        $a['canceled_avg_pay_price'] = $order_status == 'canceled' ? $sale_price : 0;
                        $a['canceled_avg_discount'] = $order_status == 'canceled' ? $discount_price : 0;
                        $a['canceled_count_discount'] = $order_status == 'canceled' ? $count_discount : 0;
                        // $a['cancel_purchase_sequence_day'] = $orderProduct->cancel_purchase_sequence_day;

                        HistoryCustomerOrderProduct::create($a);
                    }






                }
            }

        }

    }
}
