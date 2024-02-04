<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Customer;
use App\Models\HistoryCustomerOrderProduct;
use App\Models\Order;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Request;

class HistoryCustomerOrderProductController extends Controller
{
    public function set_hc_order_product_table_refresh_all()
    {

        $input = Request()->all();

        Helper::DBConnection(env('SERVER_STATUS', '') . 'utopia_store_' . $input['idb']);

        $customer = Customer::select('id')->get();

        HistoryCustomerOrderProduct::truncate();

        for ($i = 0; $i < count($customer); $i++) {

            $order = Order::where('customer_id', $customer[$i]->id)->get();
            // echo 'customer_id '. $customer[$i]->id .' order ' .($order) ."\n";
            for ($j = 0; $j < count($order); $j++) {

                $product_id = json_decode($order[$j]->product_id);
                // echo ' product_id string ' . ($order[$j]->product_id) . "\n";
                for ($k = 0; $k < count($product_id); $k++) {
                    // echo ' product_id [$k] ' . ($product_id[$k]) . "\n";

                    $order_status_id = $order[$j]->order_status_id;
                    $key = array_search($product_id[$k], $product_id);

                    $count_selected_array = json_decode($order[$j]->count_selected);
                    $count_selected = $count_selected_array[$key] ?? 1;
                    $sale_price_array = json_decode($order[$j]->sale_price);
                    $sale_price = $sale_price_array[$key] ?? 0;
                    $discount_price_array = json_decode($order[$j]->discount_price);
                    $discount_price = $discount_price_array[$key] ?? 0;
                    $order_discount_times = $discount_price > 0 ? 1 : 0;
                    try {
                        $delivery_price = intval($order[$j]->delivery_type_price);
                    } catch (\Throwable $th) {
                        $delivery_price = -1;
                    }
                    $order_discount_times = $discount_price > 0 ? 1 : 0;
                    try {
                        $all_count_products = intval($order[$j]->all_count_products);
                    } catch (\Throwable $th) {
                        $all_count_products = count($count_selected_array);
                    }
                    $all_order_free_delivery_times = $delivery_price == 0 ? 1 : 0;

                    if (HistoryCustomerOrderProduct::where('customer_id', $customer[$i]->id)->where('product_id', $product_id[$k])->exists()) {
                        $orderProduct = HistoryCustomerOrderProduct::where('customer_id', $customer[$i]->id)->where('product_id', $product_id[$k])->first();

                        $a = array();
                        $a['customer_id'] = $customer[$i]->id;
                        $a['product_id'] = $product_id[$k];

                        $a['all_order_times'] = intval($orderProduct->all_order_times) + 1;
                        $a['all_order_discount_times'] = intval($orderProduct->all_order_discount_times) + $order_discount_times;
                        $a['all_order_free_delivery_times'] = intval($orderProduct->all_order_free_delivery_times) + $all_order_free_delivery_times;
                        $a['all_product_count'] = intval($orderProduct->all_product_count) + $count_selected;
                        $a['all_product_discount_count'] = intval($orderProduct->all_product_discount_count) + ($order_discount_times * $count_selected);
                        $a['all_product_free_delivery_count'] = intval($orderProduct->all_product_free_delivery_count) + ($all_order_free_delivery_times * $count_selected);
                        $a['all_total_product_pay_price'] = intval($orderProduct->all_total_product_pay_price) + round($sale_price * $count_selected);
                        $a['all_total_product_discount'] = intval($orderProduct->all_total_product_discount) + round($order_discount_times * $discount_price * $count_selected);
                        $a['all_avg_product_pay_price'] = round($a['all_total_product_pay_price'] / $a['all_product_count']);
                        $a['all_avg_product_discount'] = $a['all_product_discount_count'] > 0 ? round($a['all_total_product_discount'] / $a['all_product_discount_count']) : 0;
                        $a['all_total_order_delivery'] = intval($orderProduct->all_total_order_delivery) + round($delivery_price);
                        $a['all_total_product_delivery'] = intval($orderProduct->all_total_product_delivery) + round($delivery_price / $all_count_products);
                        $a['all_avg_product_delivery'] = ($a['all_product_count'] - $a['all_product_free_delivery_count']) > 0 ? round($a['all_total_product_delivery'] / ($a['all_product_count'] - $a['all_product_free_delivery_count'])) : 0;
                        $a['all_order_last_date'] = verta($order[$j]->created_at);

                        $fristDate = $orderProduct->all_order_first_date;
                        $lastDatelast = $orderProduct->all_order_last_date;
                        $lastDateNow = strval(verta($order[$j]->created_at));
                        $lastDate = SUBSTR($lastDateNow, 0, 10);
                        $diffDays = verta($lastDatelast)->diffDays($lastDateNow, false);
                        $diffDaysWithFirst = verta($lastDatelast)->diffDays($fristDate, false);

                        // dd($fristDate, $lastDatelast ,$lastDateNow , $lastDate, $diffDays ,$diffDaysWithFirst);
                        $all_purchase_date = array();
                        // dd(json_decode($orderProduct->all_purchase_date) ,json_decode('["1402-11-09"]'), $orderProduct->all_purchase_date);
                        $orderProduct->all_purchase_date != null ? $all_purchase_date = json_decode($orderProduct->all_purchase_date) : null;
                        array_push($all_purchase_date, $lastDate);

                        $all_purchase_day = array();
                        $orderProduct->all_purchase_date != null ? $all_purchase_day = json_decode($orderProduct->all_purchase_day) : null;
                        array_push($all_purchase_day, abs($diffDaysWithFirst));

                        $all_purchase_sequence_day = array();
                        $orderProduct->all_purchase_date != null ? $all_purchase_sequence_day = json_decode($orderProduct->all_purchase_sequence_day) : null;
                        // if ($customer[$i]->id == 2 && $product_id[$k]) {
                        //     echo $diffDays . "\n";
                        // }
                        array_push($all_purchase_sequence_day, abs($diffDays));

                        // $all_purchase_sequence_day = array_filter($all_purchase_sequence_day);
                        // dd(array_sum($all_purchase_sequence_day), count($all_purchase_sequence_day));
                        $all_purchase_avg_sequence_day = count($all_purchase_sequence_day) == 0 ? 0 : array_sum($all_purchase_sequence_day) / count($all_purchase_sequence_day);

                        $all_purchase_pressture_day = $this->get_pressture_day($all_purchase_sequence_day);
                        $all_purchase_power_in_month = $this->get_power_in_month($all_purchase_date);
                        $all_purchase_power_in_year = $this->get_power_in_year($all_purchase_date);

                        // $k == 2 ? dd($k, $all_purchase_date, $all_purchase_day, $all_purchase_sequence_day, $all_purchase_avg_sequence_day, $all_purchase_pressture_day, $all_purchase_power_in_month, $all_purchase_pressture_day) : null;

                        $a['all_purchase_date'] = $all_purchase_date;
                        $a['all_purchase_day'] = $all_purchase_day;
                        $a['all_purchase_total_day'] = abs($diffDaysWithFirst);
                        $a['all_purchase_sequence_day'] = $all_purchase_sequence_day;
                        $a['all_purchase_avg_sequence_day'] = round($all_purchase_avg_sequence_day);
                        $a['all_purchase_pressture_day'] = $all_purchase_pressture_day;
                        $a['all_purchase_power_in_month'] = $all_purchase_power_in_month;
                        $a['all_purchase_power_in_year'] = $all_purchase_power_in_year;

                        if ($order_status_id <= 5) {
                            // $order_status = 'pending';

                            $a['pending_order_times'] = intval($orderProduct->pending_order_times) + 1;
                            $a['pending_order_discount_times'] = intval($orderProduct->pending_order_discount_times) + $order_discount_times;
                            $a['pending_order_free_delivery_times'] = intval($orderProduct->pending_order_free_delivery_times) + $all_order_free_delivery_times;
                            $a['pending_product_count'] = intval($orderProduct->pending_product_count) + $count_selected;
                            $a['pending_product_discount_count'] = intval($orderProduct->pending_product_discount_count) + ($order_discount_times * $count_selected);
                            $a['pending_product_free_delivery_count'] = intval($orderProduct->pending_product_free_delivery_count) + ($all_order_free_delivery_times * $count_selected);
                            $a['pending_total_product_pay_price'] = intval($orderProduct->pending_total_product_pay_price) + round($sale_price * $count_selected);
                            $a['pending_total_product_discount'] = intval($orderProduct->pending_total_product_discount) + round($order_discount_times * $discount_price * $count_selected);
                            $a['pending_avg_product_pay_price'] = round($a['pending_total_product_pay_price'] / $a['pending_product_count']);
                            $a['pending_avg_product_discount'] = $a['pending_product_discount_count'] > 0 ? round($a['pending_total_product_discount'] / $a['pending_product_discount_count']) : 0;
                            $a['pending_total_order_delivery'] = intval($orderProduct->pending_total_order_delivery) + round($delivery_price);
                            $a['pending_total_product_delivery'] = intval($orderProduct->pending_total_product_delivery) + round($delivery_price / $all_count_products);
                            $a['pending_avg_product_delivery'] = ($a['pending_product_count'] - $a['pending_product_free_delivery_count']) > 0 ? round($a['pending_total_product_delivery'] / ($a['pending_product_count'] - $a['pending_product_free_delivery_count'])) : 0;
                            $a['pending_order_last_date'] = verta($order[$j]->created_at);

                            $fristDate = verta($orderProduct->pending_order_first_date);
                            $lastDatelast = verta($orderProduct->pending_order_last_date);
                            $lastDateNow = strval(verta($order[$j]->created_at));
                            $lastDate = SUBSTR($lastDateNow, 0, 10);
                            $diffDays = verta($lastDatelast)->diffDays($lastDateNow, false);
                            $diffDaysWithFirst = verta($lastDatelast)->diffDays($fristDate, false);

                            $pending_purchase_date = array();
                            $orderProduct->pending_purchase_date != null ? $pending_purchase_date = json_decode($orderProduct->pending_purchase_date) : null;
                            array_push($pending_purchase_date, $lastDate);

                            $pending_purchase_day = array();
                            $orderProduct->pending_purchase_date != null ? $pending_purchase_day = json_decode($orderProduct->pending_purchase_day) : null;
                            array_push($pending_purchase_day, abs($diffDaysWithFirst));

                            $pending_purchase_sequence_day = array();
                            $orderProduct->pending_purchase_date != null ? $pending_purchase_sequence_day = json_decode($orderProduct->pending_purchase_sequence_day) : null;
                            array_push($pending_purchase_sequence_day, abs($diffDays));

                            $pending_purchase_sequence_day = array_filter($pending_purchase_sequence_day);
                            $pending_purchase_avg_sequence_day = count($pending_purchase_sequence_day) == 0 ? 0 : array_sum($pending_purchase_sequence_day) / count($pending_purchase_sequence_day);

                            $pending_purchase_pressture_day = $this->get_pressture_day($pending_purchase_sequence_day);
                            $pending_purchase_power_in_month = $this->get_power_in_month($pending_purchase_date);
                            $pending_purchase_power_in_year = $this->get_power_in_year($pending_purchase_date);

                            $a['pending_purchase_date'] = $pending_purchase_date;
                            $a['pending_purchase_day'] = $pending_purchase_day;
                            $a['pending_purchase_total_day'] = abs($diffDaysWithFirst);
                            $a['pending_purchase_sequence_day'] = $pending_purchase_sequence_day;
                            $a['pending_purchase_avg_sequence_day'] = round($pending_purchase_avg_sequence_day);
                            $a['pending_purchase_pressture_day'] = $pending_purchase_pressture_day;
                            $a['pending_purchase_power_in_month'] = $pending_purchase_power_in_month;
                            $a['pending_purchase_power_in_year'] = $pending_purchase_power_in_year;
                        } else if ($order_status_id == 8) {
                            // $order_status = 'canceled';

                            $a['canceled_order_times'] = intval($orderProduct->canceled_order_times) + 1;
                            $a['canceled_order_discount_times'] = intval($orderProduct->canceled_order_discount_times) + $order_discount_times;
                            $a['canceled_order_free_delivery_times'] = intval($orderProduct->canceled_order_free_delivery_times) + $all_order_free_delivery_times;
                            $a['canceled_product_count'] = intval($orderProduct->canceled_product_count) + $count_selected;
                            $a['canceled_product_discount_count'] = intval($orderProduct->canceled_product_discount_count) + ($order_discount_times * $count_selected);
                            $a['canceled_product_free_delivery_count'] = intval($orderProduct->canceled_product_free_delivery_count) + ($all_order_free_delivery_times * $count_selected);
                            $a['canceled_total_product_pay_price'] = intval($orderProduct->canceled_total_product_pay_price) + round($sale_price * $count_selected);
                            $a['canceled_total_product_discount'] = intval($orderProduct->canceled_total_product_discount) + round($order_discount_times * $discount_price * $count_selected);
                            $a['canceled_avg_product_pay_price'] = round($a['canceled_total_product_pay_price'] / $a['canceled_product_count']);
                            $a['canceled_avg_product_discount'] = $a['canceled_product_discount_count'] > 0 ? round($a['canceled_total_product_discount'] / $a['canceled_product_discount_count']) : 0;
                            $a['canceled_total_order_delivery'] = intval($orderProduct->canceled_total_order_delivery) + round($delivery_price);
                            $a['canceled_total_product_delivery'] = intval($orderProduct->canceled_total_product_delivery) + round($delivery_price / $all_count_products);
                            $a['canceled_avg_product_delivery'] = ($a['canceled_product_count'] - $a['canceled_product_free_delivery_count']) > 0 ? round($a['canceled_total_product_delivery'] / ($a['canceled_product_count'] - $a['canceled_product_free_delivery_count'])) : 0;
                            $a['canceled_order_last_date'] = verta($order[$j]->created_at);

                            $fristDate = verta($orderProduct->canceled_order_first_date);
                            $lastDatelast = verta($orderProduct->canceled_order_last_date);
                            $lastDateNow = strval(verta($order[$j]->created_at));
                            $lastDate = SUBSTR($lastDateNow, 0, 10);
                            $diffDays = verta($lastDatelast)->diffDays($lastDateNow, false);
                            $diffDaysWithFirst = verta($lastDatelast)->diffDays($fristDate, false);

                            $canceled_purchase_date = array();
                            $orderProduct->canceled_purchase_date != null ? $canceled_purchase_date = json_decode($orderProduct->canceled_purchase_date) : null;
                            array_push($canceled_purchase_date, $lastDate);

                            $canceled_purchase_day = array();
                            $orderProduct->canceled_purchase_date != null ? $canceled_purchase_day = json_decode($orderProduct->canceled_purchase_day) : null;
                            array_push($canceled_purchase_day, abs($diffDaysWithFirst));

                            $canceled_purchase_sequence_day = array();
                            $orderProduct->canceled_purchase_date != null ? $canceled_purchase_sequence_day = json_decode($orderProduct->canceled_purchase_sequence_day) : null;
                            array_push($canceled_purchase_sequence_day, abs($diffDays));

                            $canceled_purchase_sequence_day = array_filter($canceled_purchase_sequence_day);
                            $canceled_purchase_avg_sequence_day = count($canceled_purchase_sequence_day) == 0 ? 0 : array_sum($canceled_purchase_sequence_day) / count($canceled_purchase_sequence_day);

                            $canceled_purchase_pressture_day = $this->get_pressture_day($canceled_purchase_sequence_day);
                            $canceled_purchase_power_in_month = $this->get_power_in_month($canceled_purchase_date);
                            $canceled_purchase_power_in_year = $this->get_power_in_year($canceled_purchase_date);

                            $a['canceled_purchase_date'] = $canceled_purchase_date;
                            $a['canceled_purchase_day'] = $canceled_purchase_day;
                            $a['canceled_purchase_total_day'] = abs($diffDaysWithFirst);
                            $a['canceled_purchase_sequence_day'] = $canceled_purchase_sequence_day;
                            $a['canceled_purchase_avg_sequence_day'] = round($canceled_purchase_avg_sequence_day);
                            $a['canceled_purchase_pressture_day'] = $canceled_purchase_pressture_day;
                            $a['canceled_purchase_power_in_month'] = $canceled_purchase_power_in_month;
                            $a['canceled_purchase_power_in_year'] = $canceled_purchase_power_in_year;
                        } else if ($order_status_id == 14) {
                            // $order_status = 'returned';
                            $a['returned_order_times'] = intval($orderProduct->returned_order_times) + 1;
                            $a['returned_order_discount_times'] = intval($orderProduct->returned_order_discount_times) + $order_discount_times;
                            $a['returned_order_free_delivery_times'] = intval($orderProduct->returned_order_free_delivery_times) + $all_order_free_delivery_times;
                            $a['returned_product_count'] = intval($orderProduct->returned_product_count) + $count_selected;
                            $a['returned_product_discount_count'] = intval($orderProduct->returned_product_discount_count) + ($order_discount_times * $count_selected);
                            $a['returned_product_free_delivery_count'] = intval($orderProduct->returned_product_free_delivery_count) + ($all_order_free_delivery_times * $count_selected);
                            $a['returned_total_product_pay_price'] = intval($orderProduct->returned_total_product_pay_price) + round($sale_price * $count_selected);
                            $a['returned_total_product_discount'] = intval($orderProduct->returned_total_product_discount) + round($order_discount_times * $discount_price * $count_selected);
                            $a['returned_avg_product_pay_price'] = round($a['returned_total_product_pay_price'] / $a['returned_product_count']);
                            $a['returned_avg_product_discount'] = $a['returned_product_discount_count'] > 0 ? round($a['returned_total_product_discount'] / $a['returned_product_discount_count']) : 0;
                            $a['returned_total_order_delivery'] = intval($orderProduct->returned_total_order_delivery) + round($delivery_price);
                            $a['returned_total_product_delivery'] = intval($orderProduct->returned_total_product_delivery) + round($delivery_price / $all_count_products);
                            $a['returned_avg_product_delivery'] = ($a['returned_product_count'] - $a['returned_product_free_delivery_count']) > 0 ? round($a['returned_total_product_delivery'] / ($a['returned_product_count'] - $a['returned_product_free_delivery_count'])) : 0;
                            $a['returned_order_last_date'] = verta($order[$j]->created_at);

                            $fristDate = verta($orderProduct->returned_order_first_date);
                            $lastDatelast = verta($orderProduct->returned_order_last_date);
                            $lastDateNow = strval(verta($order[$j]->created_at));
                            $lastDate = SUBSTR($lastDateNow, 0, 10);
                            $diffDays = verta($lastDatelast)->diffDays($lastDateNow, false);
                            $diffDaysWithFirst = verta($lastDatelast)->diffDays($fristDate, false);

                            $returned_purchase_date = array();
                            $orderProduct->returned_purchase_date != null ? $returned_purchase_date = json_decode($orderProduct->returned_purchase_date) : null;
                            array_push($returned_purchase_date, $lastDate);

                            $returned_purchase_day = array();
                            $orderProduct->returned_purchase_date != null ? $returned_purchase_day = json_decode($orderProduct->returned_purchase_day) : null;
                            array_push($returned_purchase_day, abs($diffDaysWithFirst));

                            $returned_purchase_sequence_day = array();
                            $orderProduct->returned_purchase_date != null ? $returned_purchase_sequence_day = json_decode($orderProduct->returned_purchase_sequence_day) : null;
                            array_push($returned_purchase_sequence_day, abs($diffDays));

                            $returned_purchase_sequence_day = array_filter($returned_purchase_sequence_day);
                            $returned_purchase_avg_sequence_day = count($returned_purchase_sequence_day) == 0 ? 0 : array_sum($returned_purchase_sequence_day) / count($returned_purchase_sequence_day);

                            $returned_purchase_pressture_day = $this->get_pressture_day($returned_purchase_sequence_day);
                            $returned_purchase_power_in_month = $this->get_power_in_month($returned_purchase_date);
                            $returned_purchase_power_in_year = $this->get_power_in_year($returned_purchase_date);

                            $a['returned_purchase_date'] = $returned_purchase_date;
                            $a['returned_purchase_day'] = $returned_purchase_day;
                            $a['returned_purchase_total_day'] = abs($diffDaysWithFirst);
                            $a['returned_purchase_sequence_day'] = $returned_purchase_sequence_day;
                            $a['returned_purchase_avg_sequence_day'] = round($returned_purchase_avg_sequence_day);
                            $a['returned_purchase_pressture_day'] = $returned_purchase_pressture_day;
                            $a['returned_purchase_power_in_month'] = $returned_purchase_power_in_month;
                            $a['returned_purchase_power_in_year'] = $returned_purchase_power_in_year;
                        } else {
                            // $order_status = 'delivered';
                            $a['delivered_order_times'] = intval($orderProduct->delivered_order_times) + 1;
                            $a['delivered_order_discount_times'] = intval($orderProduct->delivered_order_discount_times) + $order_discount_times;
                            $a['delivered_order_free_delivery_times'] = intval($orderProduct->delivered_order_free_delivery_times) + $all_order_free_delivery_times;
                            $a['delivered_product_count'] = intval($orderProduct->delivered_product_count) + $count_selected;
                            $a['delivered_product_discount_count'] = intval($orderProduct->delivered_product_discount_count) + ($order_discount_times * $count_selected);
                            $a['delivered_product_free_delivery_count'] = intval($orderProduct->delivered_product_free_delivery_count) + ($all_order_free_delivery_times * $count_selected);
                            $a['delivered_total_product_pay_price'] = intval($orderProduct->delivered_total_product_pay_price) + round($sale_price * $count_selected);
                            $a['delivered_total_product_discount'] = intval($orderProduct->delivered_total_product_discount) + round($order_discount_times * $discount_price * $count_selected);
                            $a['delivered_avg_product_pay_price'] = round($a['delivered_total_product_pay_price'] / $a['delivered_product_count']);
                            $a['delivered_avg_product_discount'] = $a['delivered_product_discount_count'] > 0 ? round($a['delivered_total_product_discount'] / $a['delivered_product_discount_count']) : 0;
                            $a['delivered_total_order_delivery'] = intval($orderProduct->delivered_total_order_delivery) + round($delivery_price);
                            $a['delivered_total_product_delivery'] = intval($orderProduct->delivered_total_product_delivery) + round($delivery_price / $all_count_products);
                            $a['delivered_avg_product_delivery'] = ($a['delivered_product_count'] - $a['delivered_product_free_delivery_count']) > 0 ? round($a['delivered_total_product_delivery'] / ($a['delivered_product_count'] - $a['delivered_product_free_delivery_count'])) : 0;
                            $a['delivered_order_last_date'] = verta($order[$j]->created_at);

                            $fristDate = verta($orderProduct->delivered_order_first_date);
                            $lastDatelast = verta($orderProduct->delivered_order_last_date);
                            $lastDateNow = strval(verta($order[$j]->created_at));
                            $lastDate = SUBSTR($lastDateNow, 0, 10);
                            $diffDays = verta($lastDatelast)->diffDays($lastDateNow, false);
                            $diffDaysWithFirst = verta($lastDatelast)->diffDays($fristDate, false);

                            $delivered_purchase_date = array();
                            $orderProduct->delivered_purchase_date != null ? $delivered_purchase_date = json_decode($orderProduct->delivered_purchase_date) : null;
                            array_push($delivered_purchase_date, $lastDate);

                            $delivered_purchase_day = array();
                            $orderProduct->delivered_purchase_date != null ? $delivered_purchase_day = json_decode($orderProduct->delivered_purchase_day) : null;
                            array_push($delivered_purchase_day, abs($diffDaysWithFirst));

                            $delivered_purchase_sequence_day = array();
                            $orderProduct->delivered_purchase_date != null ? $delivered_purchase_sequence_day = json_decode($orderProduct->delivered_purchase_sequence_day) : null;
                            array_push($delivered_purchase_sequence_day, abs($diffDays));

                            $delivered_purchase_sequence_day = array_filter($delivered_purchase_sequence_day);
                            $delivered_purchase_avg_sequence_day = count($delivered_purchase_sequence_day) == 0 ? 0 : array_sum($delivered_purchase_sequence_day) / count($delivered_purchase_sequence_day);

                            $delivered_purchase_pressture_day = $this->get_pressture_day($delivered_purchase_sequence_day);
                            $delivered_purchase_power_in_month = $this->get_power_in_month($delivered_purchase_date);
                            $delivered_purchase_power_in_year = $this->get_power_in_year($delivered_purchase_date);

                            $a['delivered_purchase_date'] = $delivered_purchase_date;
                            $a['delivered_purchase_day'] = $delivered_purchase_day;
                            $a['delivered_purchase_total_day'] = abs($diffDaysWithFirst);
                            $a['delivered_purchase_sequence_day'] = $delivered_purchase_sequence_day;
                            $a['delivered_purchase_avg_sequence_day'] = round($delivered_purchase_avg_sequence_day);
                            $a['delivered_purchase_pressture_day'] = $delivered_purchase_pressture_day;
                            $a['delivered_purchase_power_in_month'] = $delivered_purchase_power_in_month;
                            $a['delivered_purchase_power_in_year'] = $delivered_purchase_power_in_year;

                        }

                        HistoryCustomerOrderProduct::where('customer_id', $customer[$i]->id)->where('product_id', $product_id[$k])->update($a);
                    } else {

                        if ($order_status_id <= 5) {
                            $order_status = 'pending';
                        } else if ($order_status_id == 8) {
                            $order_status = 'canceled';
                        } else if ($order_status_id == 14) {
                            $order_status = 'returned';
                        } else {
                            $order_status = 'delivered';
                        }

                        $all_purchase_power_in_month = $this->get_power_in_month([$order[$j]->created_at]);
                        $all_purchase_power_in_year = $this->get_power_in_year([$order[$j]->created_at]);

                        $a = array();
                        $a['customer_id'] = $customer[$i]->id;
                        $a['product_id'] = $product_id[$k];

                        $a['all_order_times'] = 1;
                        $a['all_order_discount_times'] = $order_discount_times;
                        $a['all_order_free_delivery_times'] = $all_order_free_delivery_times;
                        $a['all_product_count'] = $count_selected;
                        $a['all_product_discount_count'] = $order_discount_times * $count_selected;
                        $a['all_product_free_delivery_count'] = $all_order_free_delivery_times * $count_selected;
                        $a['all_total_product_pay_price'] = round($sale_price * $count_selected);
                        $a['all_total_product_discount'] = round($order_discount_times * $discount_price * $count_selected);
                        $a['all_avg_product_pay_price'] = round($a['all_total_product_pay_price'] / $a['all_product_count']);
                        $a['all_avg_product_discount'] = $order_discount_times > 0 ? round($a['all_total_product_discount'] / $a['all_product_discount_count']) : 0;
                        $a['all_total_order_delivery'] = round($delivery_price);
                        $a['all_total_product_delivery'] = round($delivery_price / $all_count_products);
                        $a['all_avg_product_delivery'] = ($a['all_product_count'] - $a['all_product_free_delivery_count']) > 0 ? round($a['all_total_product_delivery'] / ($a['all_product_count'] - $a['all_product_free_delivery_count'])) : 0;
                        $a['all_order_first_date'] = verta($order[$j]->created_at);
                        $a['all_order_last_date'] = verta($order[$j]->created_at);
                        $a['all_purchase_date'] = '["' . verta($order[$j]->created_at) . '"]';
                        $a['all_purchase_day'] = '[0]';
                        $a['all_purchase_total_day'] = 0;
                        $a['all_purchase_sequence_day'] = '[]';
                        $a['all_purchase_avg_sequence_day'] = 0;
                        $a['all_purchase_pressture_day'] = 'first';
                        $a['all_purchase_power_in_month'] = $all_purchase_power_in_month;
                        $a['all_purchase_power_in_year'] = $all_purchase_power_in_year;

                        $a['pending_order_times'] = $order_status == 'pending' ? 1 : 0;
                        $a['pending_order_discount_times'] = $order_status == 'pending' ? $order_discount_times : 0;
                        $a['pending_order_free_delivery_times'] = $order_status == 'pending' ? $all_order_free_delivery_times : 0;
                        $a['pending_product_count'] = $order_status == 'pending' ? $count_selected : 0;
                        $a['pending_product_discount_count'] = $order_status == 'pending' ? $order_discount_times * $count_selected : 0;
                        $a['pending_product_free_delivery_count'] = $order_status == 'pending' ? $all_order_free_delivery_times * $count_selected : 0;
                        $a['pending_total_product_pay_price'] = $order_status == 'pending' ? round($sale_price * $count_selected) : 0;
                        $a['pending_total_product_discount'] = $order_status == 'pending' ? round($order_discount_times * $discount_price * $count_selected) : 0;
                        $a['pending_avg_product_pay_price'] = $order_status == 'pending' ? round($a['pending_total_product_pay_price'] / $a['pending_product_count']) : 0;
                        $a['pending_avg_product_discount'] = $order_status == 'pending' ? $order_discount_times > 0 ? round($a['pending_total_product_discount'] / $a['pending_product_discount_count']) : 0 : 0;
                        $a['pending_total_order_delivery'] = $order_status == 'pending' ? round($delivery_price) : 0;
                        $a['pending_total_product_delivery'] = $order_status == 'pending' ? round($delivery_price / $all_count_products) : 0;
                        $a['pending_avg_product_delivery'] = $order_status == 'pending' ? ($a['pending_product_count'] - $a['pending_product_free_delivery_count']) > 0 ? round($a['pending_total_product_delivery'] / ($a['pending_product_count'] - $a['pending_product_free_delivery_count'])) : 0 : 0;
                        $a['pending_order_first_date'] = $order_status == 'pending' ? verta($order[$j]->created_at) : null;
                        $a['pending_order_last_date'] = $order_status == 'pending' ? verta($order[$j]->created_at) : null;
                        $a['pending_purchase_date'] = $order_status == 'pending' ? '["' . verta($order[$j]->created_at) . '"]' : '[]';
                        $a['pending_purchase_day'] = $order_status == 'pending' ? '[0]' : '[]';
                        $a['pending_purchase_total_day'] = 0;
                        $a['pending_purchase_sequence_day'] = '[]';
                        $a['pending_purchase_avg_sequence_day'] = 0;
                        $a['pending_purchase_pressture_day'] = $order_status == 'pending' ? 'first' : null;
                        $a['pending_purchase_power_in_month'] = $order_status == 'pending' ? $all_purchase_power_in_month : null;
                        $a['pending_purchase_power_in_year'] = $order_status == 'pending' ? $all_purchase_power_in_year : null;

                        $a['delivered_order_times'] = $order_status == 'delivered' ? 1 : 0;
                        $a['delivered_order_discount_times'] = $order_status == 'delivered' ? $order_discount_times : 0;
                        $a['delivered_order_free_delivery_times'] = $order_status == 'delivered' ? $all_order_free_delivery_times : 0;
                        $a['delivered_product_count'] = $order_status == 'delivered' ? $count_selected : 0;
                        $a['delivered_product_discount_count'] = $order_status == 'delivered' ? $order_discount_times * $count_selected : 0;
                        $a['delivered_product_free_delivery_count'] = $order_status == 'delivered' ? $all_order_free_delivery_times * $count_selected : 0;
                        $a['delivered_total_product_pay_price'] = $order_status == 'delivered' ? round($sale_price * $count_selected) : 0;
                        $a['delivered_total_product_discount'] = $order_status == 'delivered' ? round($order_discount_times * $discount_price * $count_selected) : 0;
                        $a['delivered_avg_product_pay_price'] = $order_status == 'delivered' ? round($a['delivered_total_product_pay_price'] / $a['delivered_product_count']) : 0;
                        $a['delivered_avg_product_discount'] = $order_status == 'delivered' ? $order_discount_times > 0 ? round($a['delivered_total_product_discount'] / $a['delivered_product_discount_count']) : 0 : 0;
                        $a['delivered_total_order_delivery'] = $order_status == 'delivered' ? round($delivery_price) : 0;
                        $a['delivered_total_product_delivery'] = $order_status == 'delivered' ? round($delivery_price / $all_count_products) : 0;
                        $a['delivered_avg_product_delivery'] = $order_status == 'delivered' ? ($a['delivered_product_count'] - $a['delivered_product_free_delivery_count']) > 0 ? round($a['delivered_total_product_delivery'] / ($a['delivered_product_count'] - $a['delivered_product_free_delivery_count'])) : 0 : 0;
                        $a['delivered_order_first_date'] = $order_status == 'delivered' ? verta($order[$j]->created_at) : null;
                        $a['delivered_order_last_date'] = $order_status == 'delivered' ? verta($order[$j]->created_at) : null;
                        $a['delivered_purchase_date'] = $order_status == 'delivered' ? '["' . verta($order[$j]->created_at) . '"]' : '[]';
                        $a['delivered_purchase_day'] = $order_status == 'delivered' ? '[0]' : '[]';
                        $a['delivered_purchase_total_day'] = 0;
                        $a['delivered_purchase_sequence_day'] = '[]';
                        $a['delivered_purchase_avg_sequence_day'] = 0;
                        $a['delivered_purchase_pressture_day'] = $order_status == 'delivered' ? 'first' : null;
                        $a['delivered_purchase_power_in_month'] = $order_status == 'delivered' ? $all_purchase_power_in_month : null;
                        $a['delivered_purchase_power_in_year'] = $order_status == 'delivered' ? $all_purchase_power_in_year : null;

                        $a['returned_order_times'] = $order_status == 'returned' ? 1 : 0;
                        $a['returned_order_discount_times'] = $order_status == 'returned' ? $order_discount_times : 0;
                        $a['returned_order_free_delivery_times'] = $order_status == 'returned' ? $all_order_free_delivery_times : 0;
                        $a['returned_product_count'] = $order_status == 'returned' ? $count_selected : 0;
                        $a['returned_product_discount_count'] = $order_status == 'returned' ? $order_discount_times * $count_selected : 0;
                        $a['returned_product_free_delivery_count'] = $order_status == 'returned' ? $all_order_free_delivery_times * $count_selected : 0;
                        $a['returned_total_product_pay_price'] = $order_status == 'returned' ? round($sale_price * $count_selected) : 0;
                        $a['returned_total_product_discount'] = $order_status == 'returned' ? round($order_discount_times * $discount_price * $count_selected) : 0;
                        $a['returned_avg_product_pay_price'] = $order_status == 'returned' ? round($a['returned_total_product_pay_price'] / $a['returned_product_count']) : 0;
                        $a['returned_avg_product_discount'] = $order_status == 'returned' ? $order_discount_times > 0 ? round($a['returned_total_product_discount'] / $a['returned_product_discount_count']) : 0 : 0;
                        $a['returned_total_order_delivery'] = $order_status == 'returned' ? round($delivery_price) : 0;
                        $a['returned_total_product_delivery'] = $order_status == 'returned' ? round($delivery_price / $all_count_products) : 0;
                        $a['returned_avg_product_delivery'] = $order_status == 'returned' ? ($a['returned_product_count'] - $a['returned_product_free_delivery_count']) > 0 ? round($a['returned_total_product_delivery'] / ($a['returned_product_count'] - $a['returned_product_free_delivery_count'])) : 0 : 0;
                        $a['returned_order_first_date'] = $order_status == 'returned' ? verta($order[$j]->created_at) : null;
                        $a['returned_order_last_date'] = $order_status == 'returned' ? verta($order[$j]->created_at) : null;
                        $a['returned_purchase_date'] = $order_status == 'returned' ? '["' . verta($order[$j]->created_at) . '"]' : '[]';
                        $a['returned_purchase_day'] = $order_status == 'returned' ? '[0]' : '[]';
                        $a['returned_purchase_total_day'] = 0;
                        $a['returned_purchase_sequence_day'] = '[]';
                        $a['returned_purchase_avg_sequence_day'] = 0;
                        $a['returned_purchase_pressture_day'] = $order_status == 'returned' ? 'first' : null;
                        $a['returned_purchase_power_in_month'] = $order_status == 'returned' ? $all_purchase_power_in_month : null;
                        $a['returned_purchase_power_in_year'] = $order_status == 'returned' ? $all_purchase_power_in_year : null;

                        $a['canceled_order_times'] = $order_status == 'canceled' ? 1 : 0;
                        $a['canceled_order_discount_times'] = $order_status == 'canceled' ? $order_discount_times : 0;
                        $a['canceled_order_free_delivery_times'] = $order_status == 'canceled' ? $all_order_free_delivery_times : 0;
                        $a['canceled_product_count'] = $order_status == 'canceled' ? $count_selected : 0;
                        $a['canceled_product_discount_count'] = $order_status == 'canceled' ? $order_discount_times * $count_selected : 0;
                        $a['canceled_product_free_delivery_count'] = $order_status == 'canceled' ? $all_order_free_delivery_times * $count_selected : 0;
                        $a['canceled_total_product_pay_price'] = $order_status == 'canceled' ? round($sale_price * $count_selected) : 0;
                        $a['canceled_total_product_discount'] = $order_status == 'canceled' ? round($order_discount_times * $discount_price * $count_selected) : 0;
                        $a['canceled_avg_product_pay_price'] = $order_status == 'canceled' ? round($a['canceled_total_product_pay_price'] / $a['canceled_product_count']) : 0;
                        $a['canceled_avg_product_discount'] = $order_status == 'canceled' ? $order_discount_times > 0 ? round($a['canceled_total_product_discount'] / $a['canceled_product_discount_count']) : 0 : 0;
                        $a['canceled_total_order_delivery'] = $order_status == 'canceled' ? round($delivery_price) : 0;
                        $a['canceled_total_product_delivery'] = $order_status == 'canceled' ? round($delivery_price / $all_count_products) : 0;
                        $a['canceled_avg_product_delivery'] = $order_status == 'canceled' ? ($a['canceled_product_count'] - $a['canceled_product_free_delivery_count']) > 0 ? round($a['canceled_total_product_delivery'] / ($a['canceled_product_count'] - $a['canceled_product_free_delivery_count'])) : 0 : 0;
                        $a['canceled_order_first_date'] = $order_status == 'canceled' ? verta($order[$j]->created_at) : null;
                        $a['canceled_order_last_date'] = $order_status == 'canceled' ? verta($order[$j]->created_at) : null;
                        $a['canceled_purchase_date'] = $order_status == 'canceled' ? '["' . verta($order[$j]->created_at) . '"]' : '[]';
                        $a['canceled_purchase_day'] = $order_status == 'canceled' ? '[0]' : '[]';
                        $a['canceled_purchase_total_day'] = 0;
                        $a['canceled_purchase_sequence_day'] = '[]';
                        $a['canceled_purchase_avg_sequence_day'] = 0;
                        $a['canceled_purchase_pressture_day'] = $order_status == 'canceled' ? 'first' : null;
                        $a['canceled_purchase_power_in_month'] = $order_status == 'canceled' ? $all_purchase_power_in_month : null;
                        $a['canceled_purchase_power_in_year'] = $order_status == 'canceled' ? $all_purchase_power_in_year : null;

                        HistoryCustomerOrderProduct::create($a);
                    }
                }
            }
            $hc_status['hc_order_product_status'] = 1;
            Order::where('customer_id', $customer[$i]->id)->update($hc_status);
        }
    }
    public function get_pressture_day($all_purchase_sequence_day)
    {
        if (count($all_purchase_sequence_day) < 3) {
            $all_purchase_pressture_day = 'first';
        } else {
            $count_array = count($all_purchase_sequence_day);
            $sum_count = floor($count_array / 2);

            $start_count = $count_array - $sum_count;
            $even_odd = ($start_count % 2 == 0) ? 0 : 1; // 0 means even and 1 means odd
            if ($even_odd == 0) { // even
                $start_count = $start_count / 2;
            } else {
                $sum_count = $sum_count + 1;
                $start_count = ($start_count - 1) / 2;
            }

            $xStart = 0;
            for ($o = 0; $o < $sum_count; $o++) {
                $xStart = $xStart + $all_purchase_sequence_day[$o];
            }

            $xMiddle = 0;
            for ($o = $start_count; $o < $sum_count + $start_count; $o++) {
                $xMiddle = $xMiddle + $all_purchase_sequence_day[$o];
            }

            $xEnd = 0;
            for ($o = $count_array - 1; $o > $sum_count; $o--) {
                $xEnd = $xEnd + $all_purchase_sequence_day[$o];
            }

            if ($xStart == $xMiddle && $xMiddle == $xEnd) {
                $all_purchase_pressture_day = 'allOver';
            } else if ($xStart == $xMiddle && $xMiddle != $xEnd) {
                if ($xMiddle > $xEnd) {
                    $all_purchase_pressture_day = 'startMiddle';
                } else {
                    $all_purchase_pressture_day = 'end';
                }
            } else if ($xStart == $xEnd && $xStart != $xMiddle) {
                if ($xStart > $xMiddle) {
                    $all_purchase_pressture_day = 'startEnd';
                } else {
                    $all_purchase_pressture_day = 'middle';
                }
            } else if ($xMiddle == $xEnd && $xMiddle != $xStart) {
                if ($xMiddle > $xStart) {
                    $all_purchase_pressture_day = 'middleEnd';
                } else {
                    $all_purchase_pressture_day = 'start';
                }
            } else if ($xStart > $xMiddle && $xMiddle >= $xEnd || $xStart > $xEnd && $xEnd >= $xMiddle) {
                $all_purchase_pressture_day = 'start';
            } else if ($xMiddle > $xStart && $xStart >= $xEnd || $xMiddle > $xEnd && $xEnd >= $xStart) {
                $all_purchase_pressture_day = 'middle';
            } else if ($xEnd > $xStart && $xStart >= $xMiddle || $xEnd > $xMiddle && $xMiddle >= $xStart) {
                $all_purchase_pressture_day = 'end';
            }
        }
        return $all_purchase_pressture_day;
    }

    public function get_power_in_month($all_purchase_date)
    {
        $xStart = 0;
        $xMiddle = 0;
        $xEnd = 0;

        for ($i = 0; $i < count($all_purchase_date); $i++) {
            $date_array = explode('-', $all_purchase_date[$i]);
            // dd($date_array);
            $day = $date_array[2];

            if ($day <= 10) {
                $xStart++;
            } else if ($day > 10 && $day <= 20) {
                $xMiddle++;
            } else {
                $xEnd++;
            }
        }

        if ($xStart == $xMiddle && $xMiddle == $xEnd) {
            $all_purchase_power_in_month = 'allOver';
        } else if ($xStart == $xMiddle && $xMiddle != $xEnd) {
            if ($xMiddle > $xEnd) {
                $all_purchase_power_in_month = 'startMiddle';
            } else {
                $all_purchase_power_in_month = 'end';
            }
        } else if ($xStart == $xEnd && $xStart != $xMiddle) {
            if ($xStart > $xMiddle) {
                $all_purchase_power_in_month = 'startEnd';
            } else {
                $all_purchase_power_in_month = 'middle';
            }
        } else if ($xMiddle == $xEnd && $xMiddle != $xStart) {
            if ($xMiddle > $xStart) {
                $all_purchase_power_in_month = 'middleEnd';
            } else {
                $all_purchase_power_in_month = 'start';
            }
        } else if ($xStart > $xMiddle && $xMiddle >= $xEnd || $xStart > $xEnd && $xEnd >= $xMiddle) {
            $all_purchase_power_in_month = 'start';
        } else if ($xMiddle > $xStart && $xStart >= $xEnd || $xMiddle > $xEnd && $xEnd >= $xStart) {
            $all_purchase_power_in_month = 'middle';
        } else if ($xEnd > $xStart && $xStart >= $xMiddle || $xEnd > $xMiddle && $xMiddle >= $xStart) {
            $all_purchase_power_in_month = 'end';
        }
        return $all_purchase_power_in_month;
    }
    public function get_power_in_year($all_purchase_date)
    {
        $xStart = 0;
        $xMiddle = 0;
        $xEnd = 0;

        for ($i = 0; $i < count($all_purchase_date); $i++) {
            $date_array = explode('-', $all_purchase_date[$i]);
            $month = $date_array[1];

            if ($month <= 4) {
                $xStart++;
            } else if ($month > 4 && $month <= 8) {
                $xMiddle++;
            } else {
                $xEnd++;
            }
        }

        if ($xStart == $xMiddle && $xMiddle == $xEnd) {
            $all_purchase_power_in_year = 'allOver';
        } else if ($xStart == $xMiddle && $xMiddle != $xEnd) {
            if ($xMiddle > $xEnd) {
                $all_purchase_power_in_year = 'startMiddle';
            } else {
                $all_purchase_power_in_year = 'end';
            }
        } else if ($xStart == $xEnd && $xStart != $xMiddle) {
            if ($xStart > $xMiddle) {
                $all_purchase_power_in_year = 'startEnd';
            } else {
                $all_purchase_power_in_year = 'middle';
            }
        } else if ($xMiddle == $xEnd && $xMiddle != $xStart) {
            if ($xMiddle > $xStart) {
                $all_purchase_power_in_year = 'middleEnd';
            } else {
                $all_purchase_power_in_year = 'start';
            }
        } else if ($xStart > $xMiddle && $xMiddle >= $xEnd || $xStart > $xEnd && $xEnd >= $xMiddle) {
            $all_purchase_power_in_year = 'start';
        } else if ($xMiddle > $xStart && $xStart >= $xEnd || $xMiddle > $xEnd && $xEnd >= $xStart) {
            $all_purchase_power_in_year = 'middle';
        } else if ($xEnd > $xStart && $xStart >= $xMiddle || $xEnd > $xMiddle && $xMiddle >= $xStart) {
            $all_purchase_power_in_year = 'end';
        }
        return $all_purchase_power_in_year;
    }
}
