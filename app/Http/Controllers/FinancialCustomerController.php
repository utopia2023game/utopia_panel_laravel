<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\HistoryCustomerOrderProduct;
use App\Models\FinancialCustomer;
use App\Models\Product;
use Illuminate\Http\Request;

class FinancialCustomerController extends Controller
{
    public static function setFinancialCustomer()
    {
        $data = array();
        $input = Request()->all();

        Helper::DBConnection(env('SERVER_STATUS', '') . 'utopia_store_' . $input['idb']);

        $products = Product::get();


        $min_sale_price = $products->min('sale_price');
        $max_sale_price = $products->max('sale_price');

        $diff = $max_sale_price - $min_sale_price;

        $data['product_price_low_price'] = intval(round($min_sale_price));
        $data['product_price_mid_low_price'] = intval(round($min_sale_price + ($diff * 1) / 3));
        $data['product_price_mid_high_price'] = intval(round($min_sale_price + ($diff * 2) / 3));
        $data['product_price_high_price'] = intval(round($max_sale_price));

        $customer_id_array = HistoryCustomerOrderProduct::Select('customer_id')->distinct()->get()->toArray();
        $customer_avg_purchase_price_array = array();
        $customer_avg_total_purchase_price_array = array();
        
        for ($i=0; $i < count($customer_id_array); $i++) { 
            $hc_order_products = HistoryCustomerOrderProduct::where('customer_id' , $customer_id_array[$i])->get();
            array_push($customer_avg_purchase_price_array , $hc_order_products->avg('all_avg_product_pay_price'));
            array_push($customer_avg_total_purchase_price_array , $hc_order_products->avg('all_total_product_pay_price'));
        }
        

        // dd($customer_avg_purchase_price_array , $customer_avg_total_purchase_price_array);

        $min_purchase_price = min($customer_avg_purchase_price_array);
        $max_purchase_price = max($customer_avg_purchase_price_array);

        $diff = $max_purchase_price - $min_purchase_price;

        $data['avg_purchase_low_price'] = intval(round($min_purchase_price));
        $data['avg_purchase_mid_low_price'] = intval(round($min_purchase_price + ($diff * 1) / 3));
        $data['avg_purchase_mid_high_price'] = intval(round($min_purchase_price + ($diff * 2) / 3));
        $data['avg_purchase_high_price'] = intval(round($max_purchase_price));

        $min_total_purchase_price = min($customer_avg_total_purchase_price_array);
        $max_total_purchase_price = max($customer_avg_total_purchase_price_array);

        $diff_total = $max_total_purchase_price - $min_total_purchase_price;

        $data['total_purchase_low_price'] = intval(round($min_total_purchase_price));
        $data['total_purchase_mid_low_price'] = intval(round($min_total_purchase_price + ($diff_total * 1) / 3));
        $data['total_purchase_mid_high_price'] = intval(round($min_total_purchase_price + ($diff_total * 2) / 3));
        $data['total_purchase_high_price'] = intval(round($max_total_purchase_price));


        // dd($data);
        try {
            FinancialCustomer::truncate();

            FinancialCustomer::create($data);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
