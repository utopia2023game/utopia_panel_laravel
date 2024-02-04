<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Customer;
use App\Models\HistoryCustomerCart;
use App\Models\HistoryCustomerCategory;
use App\Models\HistoryCustomerLike;
use App\Models\HistoryCustomerNextCart;
use App\Models\HistoryCustomerOrderProduct;
use App\Models\HistoryCustomerShare;
use App\Models\HistoryCustomerView;
use App\Models\Product;
use Illuminate\Http\Request;

class AnalyticsCustomerController extends Controller
{
    public function customerPerfermanceComputingOprations(Request $request)
    {
        $input = Request()->all();

        Helper::DBConnection(env('SERVER_STATUS', '') . 'utopia_store_' . $input['idb']);

        // $hcView = HistoryCustomerView::Select('customer_id')->distinct()->get();
        $customers = Customer::Select('id')->get();
        // dd($customers[0]->id);

        // echo $customers;
        $dataOriginal = array();
        $dataView = array();
        $dataCategory = array();
        // $viewData = array();
        for ($i = 0; $i < count($customers); $i++) {
            $data = array();
            $productId = array();

            $data['customer_id'] = $customers[$i]->id;

            // customerIdProductId === CPid
            // $OrderCPid = Order::Select('product_id')->distinct()->where('customer_id', $data['customer_id'])->get();
            $hcOrderProductCPid = HistoryCustomerOrderProduct::Select('product_id')->distinct()->where('customer_id', $data['customer_id'])->get();
            $hcCartCPid = HistoryCustomerCart::Select('product_id')->distinct()->where('customer_id', $data['customer_id'])->get();
            $hcNextCartCPid = HistoryCustomerNextCart::Select('product_id')->distinct()->where('customer_id', $data['customer_id'])->get();
            $hcLikeCPid = HistoryCustomerLike::Select('product_id')->distinct()->where('customer_id', $data['customer_id'])->get();
            $hcViewCPid = HistoryCustomerView::Select('product_id')->distinct()->where('customer_id', $data['customer_id'])->get();
            $hcShareCPid = HistoryCustomerShare::Select('product_id')->distinct()->where('customer_id', $data['customer_id'])->get();

            for ($k = 0; $k < count($hcOrderProductCPid); $k++) {
                array_push($productId, $hcOrderProductCPid[$k]->product_id);
            }

            for ($k = 0; $k < count($hcCartCPid); $k++) {
                array_push($productId, $hcCartCPid[$k]->product_id);
            }

            for ($k = 0; $k < count($hcNextCartCPid); $k++) {
                array_push($productId, $hcNextCartCPid[$k]->product_id);
            }

            for ($k = 0; $k < count($hcLikeCPid); $k++) {
                array_push($productId, $hcLikeCPid[$k]->product_id);
            }

            for ($k = 0; $k < count($hcViewCPid); $k++) {
                array_push($productId, $hcViewCPid[$k]->product_id);
            }

            for ($k = 0; $k < count($hcShareCPid); $k++) {
                array_push($productId, $hcShareCPid[$k]->product_id);
            }
            // dd($productId );
            // echo json_encode($productId) ."\n";

            $productId = array_unique($productId);
            sort($productId);
            // echo json_encode($productId) ."\n";

            // $data['customer_id'] == 2 ? dd($data['customer_id'], $productId) : null;
            // echo $data['customer_id'] . ' ' . $productId . ' ' . $hcViewCPid . ' ' . $hcShareCPid;
            // dd($data['customer_id'],$productId,$hcViewCPid,$hcShareCPid);

            for ($j = 0; $j < count($productId); $j++) {
                // echo $productId[$j]  ." customer_id " .$data['customer_id'] ." j $j \n";
                $viewData = array();
                $viewData['product_id'] = $productId[$j];
                $viewData['hc_view_id'] = array();
                $viewData['hc_category_id'] = array();
                $viewData['hc_order_product_id'] = 0;
                $viewData['hc_like_id'] = 0;
                $viewData['hc_share_id'] = array();
                $viewData['hc_cart_id'] = array();
                $viewData['hc_next_cart_id'] = array();
                $viewData['product_count_view'] = 0;
                $viewData['product_avg_time_view'] = 0;
                $CPid = HistoryCustomerView::where('customer_id', $data['customer_id'])->where('product_id', $viewData['product_id'])->get();
                if ($CPid != null && count($CPid) > 0) {
                    for ($h = 0; $h < count($CPid); $h++) {
                        array_push($viewData['hc_view_id'], $CPid[$h]->id);
                    }
                    $viewData['product_count_view'] = count($CPid);
                    $viewData['product_avg_time_view'] = round($CPid->avg('page_view_time'));
                }

                // dd($productId[$j]);
                $viewData['category_id'] = array();
                $viewData['category_count_view'] = array();
                $viewData['category_avg_time_view'] = array();
                $categoryId = Product::Select('categories_id')->where('id', $productId[$j])->first();
                if ($categoryId != null) {
                    $categoryId = json_decode($categoryId->categories_id);
                    // dd(explode('"',$categoryId->categories_id));
                    // dd(count($categoryId));
                    for ($m = 0; $m < count($categoryId); $m++) {
                        $CPid = HistoryCustomerCategory::where('customer_id', $data['customer_id'])->where('category_id', $categoryId[$m])->get();
                        if ($CPid != null) {
                            for ($h = 0; $h < count($CPid); $h++) {
                                array_push($viewData['hc_category_id'], $CPid[$h]->id);
                            }
                            array_push($viewData['category_id'], $categoryId[$m]);
                            array_push($viewData['category_count_view'], count($CPid));
                            array_push($viewData['category_avg_time_view'], round($CPid->avg('page_view_time')));
                        }
                    }
                }

                $CPid = HistoryCustomerLike::where('customer_id', $data['customer_id'])->where('product_id', $productId[$j])->get()->last();
                $viewData['hc_like_id'] = $CPid != null ? $CPid->id : 0;
                $viewData['product_like'] = $CPid != null ? $CPid->like : 0;

                $CPid = HistoryCustomerShare::where('customer_id', $data['customer_id'])->where('product_id', $productId[$j])->get();
                if ($CPid != null && count($CPid) > 0) {
                    for ($h = 0; $h < count($CPid); $h++) {
                        array_push($viewData['hc_share_id'], $CPid[$h]->id);
                    }
                }
                $viewData['product_share_count'] = $CPid != null ? count($CPid) : 0;

                $CPid = HistoryCustomerCart::where('customer_id', $data['customer_id'])->where('product_id', $productId[$j])->get();
                if ($CPid != null && count($CPid) > 0) {
                    for ($h = 0; $h < count($CPid); $h++) {
                        array_push($viewData['hc_cart_id'], $CPid[$h]->id);
                    }
                }
                $viewData['product_cart_times'] = $CPid != null ? count($CPid) : 0;
                $viewData['product_cart_increment_decrement'] = $CPid != null ? $CPid->sum('increment_decrement') : 0;

                $CPid = HistoryCustomerNextCart::where('customer_id', $data['customer_id'])->where('product_id', $productId[$j])->get();
                if ($CPid != null && count($CPid) > 0) {
                    for ($h = 0; $h < count($CPid); $h++) {
                        array_push($viewData['hc_next_cart_id'], $CPid[$h]->id);
                    }
                }
                $viewData['product_next_cart_times'] = $CPid != null ? count($CPid) : 0;
                $viewData['product_next_cart_increment_decrement'] = $CPid != null ? $CPid->sum('increment_decrement') : 0;

                $CPid = HistoryCustomerOrderProduct::where('customer_id', $data['customer_id'])->where('product_id', $productId[$j])->first();

                // echo $CPid . "\n";
                // dd($data ['customer_id'], $productId[$j], count($CPid));

                $viewData['product_all_order_times'] = 0;
                $viewData['product_all_order_product_discount_times'] = 0;
                $viewData['product_all_order_product_free_delivery_times'] = 0;
                $viewData['product_all_order_product_count'] = 0;
                $viewData['product_all_order_product_discount_count'] = 0;
                $viewData['product_all_order_product_free_delivery_count'] = 0;
                $viewData['product_delivered_order_times'] = 0;
                $viewData['product_delivered_order_product_discount_times'] = 0;
                $viewData['product_delivered_order_product_free_delivery_times'] = 0;
                $viewData['product_delivered_order_product_count'] = 0;
                $viewData['product_delivered_order_product_discount_count'] = 0;
                $viewData['product_delivered_order_product_free_delivery_count'] = 0;
                $viewData['product_pending_order_times'] = 0;
                $viewData['product_pending_order_product_discount_times'] = 0;
                $viewData['product_pending_order_product_free_delivery_times'] = 0;
                $viewData['product_pending_order_product_count'] = 0;
                $viewData['product_pending_order_product_discount_count'] = 0;
                $viewData['product_pending_order_product_free_delivery_count'] = 0;
                if ($CPid != null) {
                    // dd($data ['customer_id'], $productId[$j],$CPid->all_order_times);
                    $viewData['hc_order_product_id'] = $CPid->id;
                    $viewData['product_all_order_times'] = $CPid->all_order_times;
                    $viewData['product_all_order_product_discount_times'] = $CPid->all_order_discount_times;
                    $viewData['product_all_order_product_free_delivery_times'] = $CPid->all_order_free_delivery_times;
                    $viewData['product_all_order_product_count'] = $CPid->all_product_count;
                    $viewData['product_all_order_product_discount_count'] = $CPid->all_product_discount_count;
                    $viewData['product_all_order_product_free_delivery_count'] = $CPid->all_product_free_delivery_count;
                    $viewData['product_delivered_order_times'] = $CPid->delivered_order_times;
                    $viewData['product_delivered_order_product_discount_times'] = $CPid->delivered_order_discount_times;
                    $viewData['product_delivered_order_product_free_delivery_times'] = $CPid->delivered_order_free_delivery_times;
                    $viewData['product_delivered_order_product_count'] = $CPid->delivered_product_count;
                    $viewData['product_delivered_order_product_discount_count'] = $CPid->delivered_product_discount_count;
                    $viewData['product_delivered_order_product_free_delivery_count'] = $CPid->delivered_product_free_delivery_count;
                    $viewData['product_pending_order_times'] = $CPid->pending_order_times;
                    $viewData['product_pending_order_product_discount_times'] = $CPid->pending_order_discount_times;
                    $viewData['product_pending_order_product_free_delivery_times'] = $CPid->pending_order_free_delivery_times;
                    $viewData['product_pending_order_product_count'] = $CPid->pending_product_count;
                    $viewData['product_pending_order_product_discount_count'] = $CPid->pending_product_discount_count;
                    $viewData['product_pending_order_product_free_delivery_count'] = $CPid->pending_product_free_delivery_count;
                }

                // $viewData['product_next_cart_count'] = $CPid != null ? count($CPid) : 0;
                // $viewData['product_next_cart_increment_decrement'] = $CPid != null ? $CPid->sum('increment_decrement') : 0;

                array_push($data, $viewData);
                // echo $data ."\n";
                // dd($data);
            }

            // for ($j = 0; $j < count($hcViewCPid); $j++) {
            //     $viewData = array();
            //     $viewData['product_id'] = $hcViewCPid[$j]->product_id;
            //     $CPid = HistoryCustomerView::where('customer_id', $data['customer_id'])->where('product_id', $viewData['product_id'])->get();
            //     $ids = '';
            //     for ($e = 0; $e < count($CPid); $e++) {
            //         // dd($CPid[$e]->id);
            //         $ids .= strval($CPid[$e]->id);
            //         // dd($ids);
            //         count($CPid) > $e + 1 ? $ids .= ',' : null;
            //         // dd($ids);
            //     }
            //     $viewData['hc_view_ids'] = '[' . $ids . ']';
            //     $viewData['product_count_view'] = count($CPid);
            //     $viewData['product_avg_time_view'] = $CPid->avg('page_view_time');

            //     array_push($data, $viewData);
            //     // dd($ids);
            //     // echo 'customer_id => ' . $customerId .'   product_id => ' . $productId .'    product_avg_time_view => ' .$product_avg_time_view . '     product_count_view => ' . $product_count_view . '     hc_view_ids => ' . $hc_view_ids . "\n";
            // }

            // $hcCuatomerCPid = HistoryCustomerCategory::Select('category_id')->distinct()->where('customer_id', $data['customer_id'])->get();

            // // dd($data['customer_id'],$hcCuatomerCPid);

            // for ($j = 0; $j < count($hcCuatomerCPid); $j++) {
            //     $viewData = array();
            //     $viewData['category_id'] = $hcCuatomerCPid[$j]->category_id;
            //     $CPid = HistoryCustomerCategory::where('customer_id', $data['customer_id'])->where('category_id', $viewData['category_id'])->get();
            //     $ids = '';
            //     for ($e = 0; $e < count($CPid); $e++) {
            //         // dd($CPid[$e]->id);
            //         $ids .= strval($CPid[$e]->id);
            //         // dd($ids);
            //         count($CPid) > $e + 1 ? $ids .= ',' : null;
            //         // dd($ids);
            //     }
            //     $viewData['hc_category_ids'] = '[' . $ids . ']';
            //     $viewData['category_count_view'] = count($CPid);
            //     $viewData['category_avg_time_view'] = $CPid->avg('page_view_time');

            //     array_push($data, $viewData);
            //     // dd($ids);
            //     // echo 'customer_id => ' . $customerId .'   product_id => ' . $productId .'    product_avg_time_view => ' .$product_avg_time_view . '     product_count_view => ' . $product_count_view . '     hc_view_ids => ' . $hc_view_ids . "\n";
            // }

            // $hcViewCustomerId = HistoryCustomerView::where('customer_id' , $hcView[$i]->customer_id)->delete();

            array_push($dataOriginal, $data);
        }

        dd($dataOriginal);
    }
}
