<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\HistoryCustomerOrderProduct;
use App\Models\OriginalFinancialRank;
use App\Models\Product;
use Illuminate\Http\Request;

class OriginalFinancialRankController extends Controller
{
    public static function setOriginalFinancialRank()
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

        $hc_order_products = HistoryCustomerOrderProduct::get();


        $min_purchase_price = $hc_order_products->min('all_avg_product_pay_price');
        $max_purchase_price = $hc_order_products->max('all_avg_product_pay_price');

        $diff = $max_purchase_price - $min_purchase_price;

        $data['avg_purchase_low_price'] = intval(round($min_purchase_price));
        $data['avg_purchase_mid_low_price'] = intval(round($min_purchase_price + ($diff * 1) / 3));
        $data['avg_purchase_mid_high_price'] = intval(round($min_purchase_price + ($diff * 2) / 3));
        $data['avg_purchase_high_price'] = intval(round($max_purchase_price));


        $min_total_purchase_price = $hc_order_products->min('all_total_product_pay_price');
        $max_total_purchase_price = $hc_order_products->max('all_total_product_pay_price');

        $diff_total = $max_total_purchase_price - $min_total_purchase_price;

        $data['total_purchase_low_price'] = intval(round($min_total_purchase_price));
        $data['total_purchase_mid_low_price'] = intval(round($min_total_purchase_price + ($diff_total * 1) / 3));
        $data['total_purchase_mid_high_price'] = intval(round($min_total_purchase_price + ($diff_total * 2) / 3));
        $data['total_purchase_high_price'] = intval(round($max_total_purchase_price));


        // dd($data);
        try {
            OriginalFinancialRank::truncate();

            OriginalFinancialRank::create($data);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
