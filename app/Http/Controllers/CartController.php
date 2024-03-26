<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Cart;
use App\Models\Media;
use App\Models\NextCart;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{

    public function listCarts()
    {

        $input = Request()->all();

        $customer_id = $input['customer_id'];

        Helper::DBConnection(env('SERVER_STATUS', '') . 'utopia_store_' . $input['idb']);

        $carts = Cart::where('customer_id', $customer_id)->get()->toArray();
        // return $carts;

        $ids = array();

        for ($i = 0; $i < count($carts); $i++) {
            array_push($ids, $carts[$i]['product_id']);
        }

        // return $ids;

        $products = array();

        for ($i = 0; $i < count($ids); $i++) {
            $product = Product::find($ids[$i]);

            // echo 'product  => ' . empty($product) . "\n";

            if (!empty($product)) {
                Helper::updatingProductsPrice($product);

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

    public function listCartsByIds()
    {

        $input = Request()->all();

        Helper::DBConnection(env('SERVER_STATUS', '') . 'utopia_store_' . $input['idb']);

        $ids = array();

        $ids = json_decode($input['ids']);

        $products = array();

        for ($i = 0; $i < count($ids); $i++) {
            $product = Product::find($ids[$i]);

            // echo 'product  => ' . empty($product) . "\n";

            if (!empty($product)) {

                Helper::updatingProductsPrice($product);
                $a = Media::where('product_id', $product['id'])->where('priority', 1)->where('type', 'image')->first();
                if ($a == null) {
                    $a = Media::where('product_id', $product['id'])->where('type', 'image')->first();
                }
                $product['thumbnail_image'] = $a == null ? "" : $a['path'];

                $product['count_selected'] = 0;
                array_push($products, $product);
            }
        }
        return $products;

    }

    public function clearCartByCustomerId()
    {

        $input = Request()->all();

        $customer_id = $input['customer_id'];

        Helper::DBConnection(env('SERVER_STATUS', '') . 'utopia_store_' . $input['idb']);

        if (Cart::where('customer_id', $customer_id)->exists()) {
            Cart::where('customer_id', $customer_id)->forcedelete();
            return 1;
        }
        return 0;

    }
    public function setIncrementDecrementCartInServer()
    {

        $input = Request()->all();

        $idb = $input['idb'];
        $data = array();
        $data['customer_id'] = $input['customer_id'];
        $data['product_id'] = $input['product_id'];
        $data['increment_decrement'] = $input['increment_decrement'];
        $data['sale_price'] = $input['sale_price'];
        $data['discount_price'] = $input['discount_price'];

        Helper::DBConnection(env('SERVER_STATUS', '') . 'utopia_store_' . $idb);

        if(!Product::where('id' , $data['product_id'])->exists()) return 0;
        $product = Product::where('id' , $data['product_id'])->first()->toArray();

        // dd($product['stack_status'] ,$product['stack_limit'] ?? $product['stack_count']);

        if($product['stack_status'] != 0) return 0;
        $count_selected_limit = $product['stack_limit'] ?? $product['stack_count'] ?? 0;

        if($count_selected_limit == 0) return 0;

        // dd($count_selected_limit);

        if (Cart::where('customer_id', $data['customer_id'])->where('product_id', $data['product_id'])->exists()) {
            $cart = Cart::where('customer_id', $data['customer_id'])->where('product_id', $data['product_id'])->first();

            if($count_selected_limit <= $cart->count_selected) return 0;
            $data['count_selected'] = $cart->count_selected + $data['increment_decrement'];

            if ($data['count_selected'] > 0) {
                unset($data['increment_decrement']);

                try {
                    $cart->update($data);
                    return 1;
                } catch (\Throwable $th) {
                    return 0;
                }
            } else {
                $cart->forcedelete();
                return 1;
            }

        } else {
            if ($data['increment_decrement'] == 1) {
                try {
                    if (NextCart::where('customer_id', $data['customer_id'])->where('product_id', $data['product_id'])->exists()) {
                        NextCart::where('customer_id', $data['customer_id'])->where('product_id', $data['product_id'])->forcedelete();
                    }
                } catch (\Throwable $th) {
                    //throw $th;
                }

                try {
                    $data['count_selected'] = 1;
                    unset($data['increment_decrement']);
                    Cart::create($data);
                    return 1;
                } catch (\Throwable $th) {
                    return 0;
                }
            }
        }

        return 0;
    }


    public function transferCartLogoutToCartLogin()
    {

        $input = Request()->all();

        $idb = $input['idb'];
        $data = array();
        $data['customer_id'] = $input['customer_id'];
        $data['product_id'] = $input['product_id'];
        $data['increment_decrement'] = $input['increment_decrement'];
        $data['sale_price'] = $input['sale_price'];
        $data['discount_price'] = $input['discount_price'];

        Helper::DBConnection(env('SERVER_STATUS', '') . 'utopia_store_' . $idb);

        if(!Product::where('id' , $data['product_id'])->exists()) return 0;
        $product = Product::where('id' , $data['product_id'])->first()->toArray();

        // dd($product['stack_status'] ,$product['stack_limit'] ?? $product['stack_count']);

        if($product['stack_status'] != 0) return 0;
        $count_selected_limit = $product['stack_limit'] ?? $product['stack_count'] ?? 0;

        if($count_selected_limit == 0) return 0;

        // dd($count_selected_limit);

        if (Cart::where('customer_id', $data['customer_id'])->where('product_id', $data['product_id'])->exists()) {
            $cart = Cart::where('customer_id', $data['customer_id'])->where('product_id', $data['product_id'])->first();

            if($count_selected_limit <= $cart->count_selected) return 0;
            $data['count_selected'] = $cart->count_selected + $data['increment_decrement'];

            if ($data['count_selected'] > 0) {
                unset($data['increment_decrement']);

                try {
                    $cart->update($data);
                    return 1;
                } catch (\Throwable $th) {
                    return 0;
                }
            } else {
                $cart->forcedelete();
                return 1;
            }

        } else {
            if ($data['increment_decrement'] == 1) {
                try {
                    if (NextCart::where('customer_id', $data['customer_id'])->where('product_id', $data['product_id'])->exists()) {
                        NextCart::where('customer_id', $data['customer_id'])->where('product_id', $data['product_id'])->forcedelete();
                    }
                } catch (\Throwable $th) {
                    //throw $th;
                }

                try {
                    $data['count_selected'] = 1;
                    unset($data['increment_decrement']);
                    Cart::create($data);
                    return 1;
                } catch (\Throwable $th) {
                    return 0;
                }
            }
        }

        return 0;
    }

    public function transferCartToNextCartInServer()
    {
        $input = Request()->all();
        $data_cart = array();

        $idb = $input['idb'];
        $data['customer_id'] = $input['customer_id'];
        $data['product_id'] = $input['product_id'];

        Helper::DBConnection(env('SERVER_STATUS', '') . 'utopia_store_' . $idb);

        if (Cart::where('customer_id', $data['customer_id'])->where('product_id', $data['product_id'])->exists()) {
            $cart = Cart::where('customer_id', $data['customer_id'])->where('product_id', $data['product_id'])->first();

            $data_cart = $cart->toArray();
            unset($data_cart['id']);
            unset($data_cart['created_at']);
            unset($data_cart['updated_at']);
            unset($data_cart['deleted_at']);

            try {
                NextCart::create($data_cart);
                $cart->forcedelete();

                return 1;
            } catch (\Throwable $th) {
                return 0 . $th;
            }
        }

        return 0;

    }

}
