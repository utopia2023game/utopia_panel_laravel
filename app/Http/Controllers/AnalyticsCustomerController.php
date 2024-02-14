<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\AnalyticsCustomer;
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
        $dataOriginal = array();
        $dataRelease = array();

        $ratio_view = 5;
        $ratio_category = 0.5;
        $ratio_pending_order_product = 3;
        $ratio_delivered_order_product = 4;
        $ratio_like = 10;
        $ratio_share = 1;
        $ratio_cart = 3;
        $ratio_next_cart = 2;
        $score_array = array();

        for ($i = 0; $i < count($customers); $i++) {
            $data = array();
            $productId = array();

            $data['customer_id'] = $customers[$i]->id;

            // customerIdProductId === CPid
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
                // $viewData = array();
                $score = 0;
                $viewData['customer_id'] = $customers[$i]->id;
                $viewData['product_id'] = $productId[$j];
                $viewData['score'] = 0;
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
                    $viewData['product_avg_time_view'] = intval(round($CPid->avg('page_view_time')));
                }
                $viewData['hc_view_id'] = json_encode($viewData['hc_view_id']);

                $score = $score + ($ratio_view * $viewData['product_count_view'] + ($viewData['product_count_view'] * $viewData['product_avg_time_view'] / 20));

                // echo '$score ratio_view ' . $score . "\n";

                $viewData['category_id'] = 0;
                $viewData['category_count_view'] = 0;
                $viewData['category_avg_time_view'] = 0;
                $categoryId = Product::Select('categories_id')->where('id', $productId[$j])->first();
                if ($categoryId != null) {
                    $categoryId = json_decode($categoryId->categories_id);
                    $cat['category_id'] = array();
                    $cat['category_count_view'] = array();
                    $cat['category_avg_time_view'] = array();
                    // dd(explode('"',$categoryId->categories_id));
                    // dd(count($categoryId));
                    for ($m = 0; $m < count($categoryId); $m++) {
                        $CPid = HistoryCustomerCategory::where('customer_id', $data['customer_id'])->where('category_id', $categoryId[$m])->get();
                        if ($CPid != null) {
                            for ($h = 0; $h < count($CPid); $h++) {
                                array_push($viewData['hc_category_id'], $CPid[$h]->id);
                            }
                            array_push($cat['category_id'], $categoryId[$m]);
                            array_push($cat['category_count_view'], count($CPid));
                            array_push($cat['category_avg_time_view'], intval(round($CPid->avg('page_view_time'))));
                        }
                    }

                    // echo '$customer_id ' . $viewData['customer_id'] . "\n";
                    // echo '$product_id ' . $viewData['product_id'] . "\n";
                    // echo '$cat[category_id] ' . json_encode($cat['category_id']) . "\n";
                    // echo '$cat[category_count_view] ' . json_encode($cat['category_count_view']) . "\n";
                    // echo '$cat[category_avg_time_view] ' . json_encode($cat['category_avg_time_view']) . "\n\n";

                    $max_num = max($cat['category_count_view']);
                    $max_key_num = array_keys($cat['category_count_view'], $max_num)[0];

                    $max_avg = max($cat['category_avg_time_view']);
                    $max_key_avg = array_keys($cat['category_avg_time_view'], $max_avg)[0];

                    // dd($cat['category_id'][0] ,$max_num , $max_key_num , $max_avg ,$max_key_avg);
                    if ($max_key_avg == $max_key_num) {
                        $viewData['category_id'] = $cat['category_id'][$max_key_num];
                        $viewData['category_count_view'] = $cat['category_count_view'][$max_key_num];
                        $viewData['category_avg_time_view'] = $cat['category_avg_time_view'][$max_key_num];
                    } else {
                        $x = array();
                        for ($n = 0; $n < count($cat['category_id']); $n++) {
                            $res = $cat['category_count_view'][$n] + ($cat['category_count_view'][$n] * $cat['category_avg_time_view'][$n] / 20);
                            array_push($x, $res);
                        }

                        if (count($x) > 0) {
                            $max_num = max($x);
                            $max_key_num = array_keys($x, $max_num)[0];

                            $viewData['category_id'] = $cat['category_id'][$max_key_num];
                            $viewData['category_count_view'] = $cat['category_count_view'][$max_key_num];
                            $viewData['category_avg_time_view'] = $cat['category_avg_time_view'][$max_key_num];
                        }

                    }

                    // $score = $score + ($ratio_category * $viewData['category_count_view'] + ($viewData['category_count_view'] * $viewData['category_avg_time_view'] / 20));
                    // echo '$score ratio_category ' . $score . "\n";
                    // dd($x , $viewData);

                    // $score_count_view = ;
                    // $score_avg_time_view = ;

                }
                // dd($score);
                // echo '$viewData[category_id] ' . $viewData['category_id'] . "\n";
                // echo '$viewData[category_count_view] ' . $viewData['category_count_view'] . "\n";
                // echo '$viewData[category_avg_time_view] ' . $viewData['category_avg_time_view'] . "\n";
                // echo '-------------------------------------------------------------' . "\n";
                $viewData['hc_category_id'] = json_encode($viewData['hc_category_id']);

                $CPid = HistoryCustomerLike::where('customer_id', $data['customer_id'])->where('product_id', $productId[$j])->get()->last();
                $viewData['hc_like_id'] = $CPid != null ? $CPid->id : 0;
                $viewData['product_like'] = $CPid != null ? $CPid->like : 0;

                $score = $score + ($ratio_like * intval($viewData['product_like']));
                // echo '$score ratio_like ' . $score . "\n";

                $CPid = HistoryCustomerShare::where('customer_id', $data['customer_id'])->where('product_id', $productId[$j])->get();
                if ($CPid != null && count($CPid) > 0) {
                    for ($h = 0; $h < count($CPid); $h++) {
                        array_push($viewData['hc_share_id'], $CPid[$h]->id);
                    }
                }
                $viewData['hc_share_id'] = json_encode($viewData['hc_share_id']);
                $viewData['product_share_count'] = $CPid != null ? count($CPid) : 0;

                $score = $score + ($ratio_share * $viewData['product_share_count']);
                // echo '$score ratio_share ' . $score . "\n";

                $CPid = HistoryCustomerCart::where('customer_id', $data['customer_id'])->where('product_id', $productId[$j])->get();
                if ($CPid != null && count($CPid) > 0) {
                    for ($h = 0; $h < count($CPid); $h++) {
                        array_push($viewData['hc_cart_id'], $CPid[$h]->id);
                    }
                }
                $viewData['hc_cart_id'] = json_encode($viewData['hc_cart_id']);
                $viewData['product_cart_times'] = $CPid != null ? count($CPid) : 0;
                $viewData['product_cart_increment_decrement'] = $CPid != null ? $CPid->sum('increment_decrement') : 0;

                $score = $score + ($ratio_cart * $viewData['product_cart_times']);
                // echo '$score ratio_cart ' . $score . "\n";

                $CPid = HistoryCustomerNextCart::where('customer_id', $data['customer_id'])->where('product_id', $productId[$j])->get();
                if ($CPid != null && count($CPid) > 0) {
                    for ($h = 0; $h < count($CPid); $h++) {
                        array_push($viewData['hc_next_cart_id'], $CPid[$h]->id);
                    }
                }
                $viewData['hc_next_cart_id'] = json_encode($viewData['hc_next_cart_id']);
                $viewData['product_next_cart_times'] = $CPid != null ? count($CPid) : 0;
                $viewData['product_next_cart_increment_decrement'] = $CPid != null ? $CPid->sum('increment_decrement') : 0;

                $score = $score + ($ratio_next_cart * $viewData['product_next_cart_times']);
                // echo '$score ratio_next_cart ' . $score . "\n";

                $CPid = HistoryCustomerOrderProduct::where('customer_id', $data['customer_id'])->where('product_id', $productId[$j])->first();

                // echo $CPid . "\n";
                // dd($data ['customer_id'], $productId[$j], count($CPid));
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

                    $score = $score + ($ratio_delivered_order_product * $viewData['product_delivered_order_times'] + $ratio_pending_order_product * $viewData['product_pending_order_times']);
                    // echo '$score ratio_delivered_order_product ' . $score . "\n";
                }

                // $viewData['product_next_cart_count'] = $CPid != null ? count($CPid) : 0;
                // $viewData['product_next_cart_increment_decrement'] = $CPid != null ? $CPid->sum('increment_decrement') : 0;

                $viewData['score'] = intval($score);

                array_push($score_array, intval($score));
                array_push($dataOriginal, $viewData);
                // echo $data ."\n";
                // dd($data);
                // echo '$customer_id ' . $viewData['customer_id'] . "\n";
                // echo '$product_id ' . $viewData['product_id'] . "\n";
                // echo '$viewData[score] ' . $viewData['score'] . "\n";
                // echo '-------------------------------------------------------------' . "\n";

            }
            // $i == 1 ? dd($score_array, $dataOriginal) : null;
            if (count($score_array) > 0) {
                for ($d = 0; $d < 6; $d++) {
                    $max_score = max($score_array);
                    $max_index = array_keys($score_array, $max_score)[0];
                    array_push($dataRelease, $dataOriginal[$max_index]);
                    unset($score_array[$max_index]);
                    unset($dataOriginal[$max_index]);
                }
            }
            // dd($score_array);
            $score_array = [];
            $dataOriginal = [];
        }

        // dd($dataRelease);
        // dd($dataOriginal);

        if (count($dataRelease) > 0) {
            AnalyticsCustomer::truncate();
        }

        for ($l = 0; $l < count($dataRelease); $l++) {
            AnalyticsCustomer::create($dataRelease[$l]);
        }

    }
}
