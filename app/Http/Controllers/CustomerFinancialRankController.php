<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerFinancialRank;
use App\Models\HistoryCustomerOrderProduct;
use App\Models\OriginalFinancialRank;

class CustomerFinancialRankController extends Controller
{
    public function setCustomerFinancialRank()
    {

        OriginalFinancialRankController::setOriginalFinancialRank();

        $original_financial_rank = OriginalFinancialRank::first();

        // $avg_purchase_low_price = $original_financial_rank->avg_purchase_low_price;
        $avg_purchase_mid_low_price = $original_financial_rank->avg_purchase_mid_low_price;
        $avg_purchase_mid_high_price = $original_financial_rank->avg_purchase_mid_high_price;
        // $avg_purchase_high_price = $original_financial_rank->avg_purchase_high_price;

        // $total_purchase_low_price = $original_financial_rank->total_purchase_low_price;
        $total_purchase_mid_low_price = $original_financial_rank->total_purchase_mid_low_price;
        $total_purchase_mid_high_price = $original_financial_rank->total_purchase_mid_high_price;
        // $total_purchase_high_price = $original_financial_rank->total_purchase_high_price;

        // dd($original_financial_rank);

        $customer = Customer::select('id')->get();

        for ($i = 0; $i < count($customer); $i++) {
            $customerId = $customer[$i]->id;

            $data = array();
            $data['avg_purchase_rank'] = null;
            $data['total_purchase_rank'] = null;

            if (HistoryCustomerOrderProduct::where('customer_id', $customerId)->exists()) {
                $hc_order_products = HistoryCustomerOrderProduct::where('customer_id', $customerId)->get();

                $avg_purchase_price = intval(round($hc_order_products->avg('all_avg_product_pay_price')));
                $total_purchase_price = intval(round($hc_order_products->avg('all_total_product_pay_price')));

                if ($avg_purchase_price < $avg_purchase_mid_low_price) {
                    $data['avg_purchase_rank'] = "low";
                } else if ($avg_purchase_price >= $avg_purchase_mid_low_price && $avg_purchase_price <= $avg_purchase_mid_high_price) {
                    $data['avg_purchase_rank'] = "mid";
                } else if ($avg_purchase_price > $avg_purchase_mid_high_price) {
                    $data['avg_purchase_rank'] = "high";
                }

                if ($total_purchase_price < $total_purchase_mid_low_price) {
                    $data['total_purchase_rank'] = "low";
                } else if ($total_purchase_price >= $total_purchase_mid_low_price && $total_purchase_price <= $total_purchase_mid_high_price) {
                    $data['total_purchase_rank'] = "mid";
                } else if ($total_purchase_price > $total_purchase_mid_high_price) {
                    $data['total_purchase_rank'] = "high";
                }

                if (CustomerFinancialRank::where('customer_id', $customerId)->exists()) {
                    CustomerFinancialRank::where('customer_id', $customerId)->update($data);
                } else {
                    $data['customer_id'] = $customerId;
                    CustomerFinancialRank::create($data);
                }
            }

            // $i == 1 ? dd($customerId, $data, $avg_purchase_price, $avg_purchase_mid_low_price, $avg_purchase_mid_high_price, $total_purchase_price, $total_purchase_mid_low_price, $total_purchase_mid_high_price) : null;
        }
        // dd($customer->id);

    }
}
