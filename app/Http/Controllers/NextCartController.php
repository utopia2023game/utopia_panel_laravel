<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Cart;
use App\Models\Media;
use App\Models\NextCart;
use App\Models\Product;
use Illuminate\Http\Request;

class NextCartController extends Controller
{
    public function listNextCarts()
    {

        $input = Request()->all();

        $customer_id = $input['customer_id'];

        Helper::DBConnection(env('SERVER_STATUS', '') . 'utopia_store_' . $input['idb']);

        $carts = NextCart::where('customer_id', $customer_id)->get()->toArray();
        // return $carts;

        $ids = array();

        for ($i = 0; $i < count($carts); $i++) {
            array_push($ids, $carts[$i]['product_id']);
        }

        // return $ids;

        $products = array();

        for ($i = 0; $i < count($ids); $i++) {
            $product = Product::find($ids[$i]);

            if (!empty($product)) {
                $result_time = Helper::updatingProductsPrice($product);

                if (!$result_time) {
                    $product['confirm_discount'] = 0;
                    $product['discount_percent'] = 0;
                    $product['discount_manual'] = 0;
                    $product['discount_price'] = 0;
                    $product['discount_time_from'] = '';
                    $product['discount_time_until'] = '';
                }

                $a = Media::where('product_id', $product['id'])->where('priority', 1)->where('type', 'image')->first();
                if ($a == null) {
                    $a = Media::where('product_id', $product['id'])->where('type', 'image')->first();
                }
                $product['thumbnail_image'] = $a == null ? "" : $a['path'];

                $product['count_selected'] = $carts[$i]['count_selected'];
                array_push($products, $product);

            }
        }
        return $products;

    }

    public function listNextCartsByIds()
    {

        $input = Request()->all();

        Helper::DBConnection(env('SERVER_STATUS', '') . 'utopia_store_' . $input['idb']);

        $ids = array();

        $ids = json_decode($input['ids']);

        // return $ids;

        $products = array();

        for ($i = 0; $i < count($ids); $i++) {
            $product = Product::find($ids[$i]);

            // echo 'product  => ' . empty($product) . "\n";

            if (!empty($product)) {
                $result_time = Helper::updatingProductsPrice($product);

                if (!$result_time) {
                    $product['confirm_discount'] = 0;
                    $product['discount_percent'] = 0;
                    $product['discount_manual'] = 0;
                    $product['discount_price'] = 0;
                    $product['discount_time_from'] = '';
                    $product['discount_time_until'] = '';
                }

                $a = Media::where('product_id', $product['id'])->where('priority', 1)->where('type', 'image')->first();
                if ($a == null) {
                    $a = Media::where('product_id', $product['id'])->where('type', 'image')->first();
                }
                $product['thumbnail_image'] = $a == null ? "" : $a['path'];

                $product['count_selected'] = 0;
                array_push($products, $product);

                // $products[count($products)] = $product;
            }
        }
        return $products;

    }

    public function transferNextCartToCartInServer()
    {
        $input = Request()->all();
        $data_cart = array();

        $idb = $input['idb'];
        $data['customer_id'] = $input['customer_id'];
        $data['product_id'] = $input['product_id'];

        Helper::DBConnection(env('SERVER_STATUS', '') . 'utopia_store_' . $idb);

        if (!Product::where('id', $data['product_id'])->exists()) {
            return 0;
        }

        $product = Product::where('id', $data['product_id'])->first()->toArray();

        if ($product['stack_status'] != 0 || $product['stack_status'] == 0 && $product['stack_count'] <= 0) {
            return 0;
        }

        $count_selected_limit = $product['stack_limit'] ?? $product['stack_count'] ?? 0;

        if ($count_selected_limit == 0) {
            return 0;
        }

        if (NextCart::where('customer_id', $data['customer_id'])->where('product_id', $data['product_id'])->exists()) {
            $next_cart = NextCart::where('customer_id', $data['customer_id'])->where('product_id', $data['product_id'])->first();

            $data_next_cart = $next_cart->toArray();
            unset($data_next_cart['id']);
            unset($data_next_cart['created_at']);
            unset($data_next_cart['updated_at']);
            unset($data_next_cart['deleted_at']);

            try {
                Cart::create($data_next_cart);
                $next_cart->forcedelete();

                return 1;
            } catch (\Throwable $th) {
                return 0;
            }
        }

        return 0;

    }

    public function transferNextCartLogoutToNextCartLogin()
    {
        $input = Request()->all();
        $data = array();

        $idb = $input['idb'];
        try {
            $data['customer_id'] = intval($input['customer_id']);
            $data['product_id'] = json_decode($input['product_id']);
            $array_count = count($data['product_id']);
            $data['count_selected'] = json_decode($input['count_selected']);
            $data['sale_price'] = json_decode($input['sale_price']);
            $data['discount_price'] = json_decode($input['discount_price']);
        } catch (\Throwable $th) {
            return 0;
        }

        if (count($data['count_selected']) != $array_count || count($data['sale_price']) != $array_count || count($data['discount_price']) != $array_count) {
            return 0;
        }

        Helper::DBConnection(env('SERVER_STATUS', '') . 'utopia_store_' . $idb);

        for ($i = 0; $i < $array_count; $i++) {

            if (!Product::where('id', $data['product_id'][$i])->exists()) {
                continue;
            }

            $product = Product::where('id', $data['product_id'][$i])->first()->toArray();

            if ($product['stack_status'] != 0) {
                continue;
            }

            $stack_count = $product['stack_count'] ?? 0;
            $count_selected_limit = $product['stack_limit'] ?? $product['stack_count'] ?? 0;

            if ($count_selected_limit == 0) {
                continue;
            }

            // dd($data['customer_id'],$data['product_id'][$i]);

            if (Cart::where('customer_id', $data['customer_id'])->where('product_id', $data['product_id'][$i])->exists()) {
                continue;
            }

            if (!NextCart::where('customer_id', $data['customer_id'])->where('product_id', $data['product_id'][$i])->exists()) {
                try {
                    $a = array();
                    $a['customer_id'] = $data['customer_id'];
                    $a['product_id'] = $data['product_id'][$i];
                    $a['count_selected'] = $data['count_selected'][$i];
                    $a['sale_price'] = $data['sale_price'][$i];
                    $a['discount_price'] = $data['discount_price'][$i];
                    NextCart::create($a);
                    continue;
                } catch (\Throwable $th) {
                    continue;
                }
            } else {
                $next_cart = NextCart::where('customer_id', $data['customer_id'])->where('product_id', $data['product_id'][$i])->first();

                if ($next_cart->count_selected + $data['count_selected'][$i] > $count_selected_limit) {
                    $data['count_selected'][$i] = $count_selected_limit;
                } else {
                    $data['count_selected'][$i] = $next_cart->count_selected + $data['count_selected'][$i];
                }

                try {
                    $a = array();
                    $a['customer_id'] = $data['customer_id'];
                    $a['product_id'] = $data['product_id'][$i];
                    $a['count_selected'] = $data['count_selected'][$i];
                    $next_cart->update($a);
                    continue;
                } catch (\Throwable $th) {
                    continue;
                }
            }
        }

        return 1;

    }

    public function deleteNextCartItemInServer()
    {
        $input = Request()->all();
        $data = array();

        $idb = $input['idb'];
        $data['customer_id'] = $input['customer_id'];
        $data['product_id'] = $input['product_id'];

        Helper::DBConnection(env('SERVER_STATUS', '') . 'utopia_store_' . $idb);

        if (NextCart::where('customer_id', $data['customer_id'])->where('product_id', $data['product_id'])->exists()) {
            $next_cart = NextCart::where('customer_id', $data['customer_id'])->where('product_id', $data['product_id'])->first();
            try {
                $next_cart->forcedelete();
                return 1;
            } catch (\Throwable $th) {
                return 0 . $th;
            }
        }

        return 0;
    }
}
