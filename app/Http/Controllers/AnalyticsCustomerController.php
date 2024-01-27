<?php

namespace App\Http\Controllers;


use App\Models\Category;
use App\Models\Order;
use App\Helpers\Helper;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\HistoryCustomerCart;
use App\Models\HistoryCustomerLike;
use App\Models\HistoryCustomerView;
use App\Models\HistoryCustomerOrder;
use App\Models\HistoryCustomerShare;
use App\Models\HistoryCustomerCategory;
use App\Models\HistoryCustomerNextCart;

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
            $OrderCPid = Order::Select('product_id')->distinct()->where('customer_id', $data['customer_id'])->get();
            // $hcOrderCPid = HistoryCustomerOrder::Select('product_id')->distinct()->where('customer_id', $data['customer_id'])->get();
            $hcCartCPid = HistoryCustomerCart::Select('product_id')->distinct()->where('customer_id', $data['customer_id'])->get();
            $hcNextCartCPid = HistoryCustomerNextCart::Select('product_id')->distinct()->where('customer_id', $data['customer_id'])->get();
            $hcLikeCPid = HistoryCustomerLike::Select('product_id')->distinct()->where('customer_id', $data['customer_id'])->get();
            $hcViewCPid = HistoryCustomerView::Select('product_id')->distinct()->where('customer_id', $data['customer_id'])->get();
            $hcShareCPid = HistoryCustomerShare::Select('product_id')->distinct()->where('customer_id', $data['customer_id'])->get();

            for ($k = 0; $k < count($OrderCPid); $k++) {
                $order_data = json_decode($OrderCPid[$k]->product_id);
                for ($h = 0; $h < count($order_data); $h++) {
                    array_push($productId, $order_data[$h]);
                }
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
                $CPid = HistoryCustomerView::where('customer_id', $data['customer_id'])->where('product_id', $viewData['product_id'])->get();
                if ($CPid != null && count($CPid) > 0) {
                    $viewData['product_count_view'] = count($CPid);
                    $viewData['product_avg_time_view'] = $CPid->avg('page_view_time');
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
                            array_push($viewData['category_id'], $categoryId[$m]);
                            array_push($viewData['category_count_view'], count($CPid));
                            array_push($viewData['category_avg_time_view'], $CPid->avg('page_view_time'));
                        }
                    }
                }

                $CPid = HistoryCustomerLike::where('customer_id', $data['customer_id'])->where('product_id', $productId[$j])->get()->last();
                $viewData['product_like'] = $CPid != null ? $CPid->like : 0;

                $CPid = HistoryCustomerShare::where('customer_id', $data['customer_id'])->where('product_id', $productId[$j])->get();
                $viewData['product_share_count'] = $CPid != null ? count($CPid) : 0;

                $CPid = HistoryCustomerCart::where('customer_id', $data['customer_id'])->where('product_id', $productId[$j])->get();
                $viewData['product_cart_count'] = $CPid != null ? count($CPid) : 0;
                $viewData['product_cart_increment_decrement'] = $CPid != null ? $CPid->sum('increment_decrement') : 0;

                $CPid = HistoryCustomerNextCart::where('customer_id', $data['customer_id'])->where('product_id', $productId[$j])->get();
                $viewData['product_next_cart_count'] = $CPid != null ? count($CPid) : 0;
                $viewData['product_next_cart_increment_decrement'] = $CPid != null ? $CPid->sum('increment_decrement') : 0;

                $CPid = Order::where('customer_id', $data['customer_id'])->get();

                $viewData['product_order_count'] = 0;
                $viewData['product_order_times'] = 0;
                // dd($CPid);
                // if ($CPid != null && count($CPid) > 0) {
                //     for ($n = 0; $n < count($CPid); $n++) {
                //         $PId = json_decode($CPid[$n]->product_id);
                //         $key = array_search($productId[$j], $PId);
                //         if ($data['customer_id'] == 2) {
                //             echo '$productId[$j] '. $productId[$j] .' key '. $key . "\n";
                //         }
                        
                    
                //         if ($key != false) {
                //             $viewData['product_order_times']++;
                //             $count_selectd = json_decode($CPid[$n]->count_selectd);
                //             if ($count_selectd != null) {
                //                 $viewData['product_order_count'] += $count_selectd[$key];
                //             }
                //         }
                //     }
                // }
                $viewData['product_next_cart_count'] = $CPid != null ? count($CPid) : 0;
                $viewData['product_next_cart_increment_decrement'] = $CPid != null ? $CPid->sum('increment_decrement') : 0;

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
