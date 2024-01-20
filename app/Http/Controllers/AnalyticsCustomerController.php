<?php

namespace App\Http\Controllers;


use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Models\HistoryCustomerView;

class AnalyticsCustomerController extends Controller
{
    public function customerPerfermanceComputingOprations(Request $request)
    {
        $input = Request()->all();

        Helper::DBConnection(env('SERVER_STATUS', '') . 'utopia_store_' . $input['idb']);

        $hcView = HistoryCustomerView::Select('customer_id')->distinct()->get();



        for ($i = 0; $i < count($hcView); $i++) {



            $hcViewCustomerIdProductId = HistoryCustomerView::Select('product_id')->distinct()->where('customer_id', $hcView[$i]->customer_id)->get();

            // dd($hcViewCustomerIdProductId[0]->product_id,$hcViewCustomerIdProductId[1]->product_id);

            for ($j = 0; $j < count($hcViewCustomerIdProductId); $j++) {

                $customerId = $hcView[$i]->customer_id;
                $productId = $hcViewCustomerIdProductId[$j]->product_id;
                $customerIdProductId = HistoryCustomerView::where('customer_id', $customerId)->where('product_id', $productId)->get();
                $ids = '';
                for ($e = 0; $e < count($customerIdProductId); $e++) {
                    // dd($customerIdProductId[$e]->id);
                    $ids .= strval($customerIdProductId[$e]->id);
                    // dd($ids);
                    count($customerIdProductId) > $e + 1 ? $ids .= ',' : null;
                    // dd($ids);
                }
                $hc_view_ids = '[' . $ids . ']';
                $product_count_view = count($customerIdProductId);
                $product_avg_time_view = $customerIdProductId->avg('page_view_time');
                // dd($ids);
                echo 'customer_id => ' . $customerId .'   product_id => ' . $productId .'    product_avg_time_view => ' .$product_avg_time_view . '     product_count_view => ' . $product_count_view . '     hc_view_ids => ' . $hc_view_ids . "\n";
            }





            // $hcViewCustomerId = HistoryCustomerView::where('customer_id' , $hcView[$i]->customer_id)->delete();

        }

    }
}
