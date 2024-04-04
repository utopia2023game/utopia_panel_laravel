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

                if ($product->stack_status != 0 || $product->stack_status == 0 && $product->stack_count <= 0) {
                    $this->dataCartToNextCart($product['id'], $customer_id, $carts[$i]);
                } else {
                    array_push($products, $product);
                }
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
            }
        }
        return $products;

    }

    public function dataCartToNextCart($product_id, $customer_id, $cart)
    {
        if (!NextCart::where('customer_id', $customer_id)->where('product_id', $product_id)->exists()) {
            try {
                NextCart::create($cart);
                Cart::where('customer_id', $customer_id)->where('product_id', $product_id)->forcedelete();
            } catch (\Throwable $th) {}
        } else {
            try {
                $cart->forcedelete();
            } catch (\Throwable $th) {}
        }
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

        if (!Product::where('id', $data['product_id'])->exists()) {
            return 0;
        }

        $product = Product::where('id', $data['product_id'])->first()->toArray();

        // dd($product['stack_status'] ,$product['stack_limit'] ?? $product['stack_count']);

        if ($product['stack_status'] != 0 || $product['stack_status'] == 0 && $product['stack_count'] <= 0) {
            return 20; // means refresh
        }

        $stack_count = $product['stack_count'] ?? 0;
        $count_selected_limit = $product['stack_limit'] ?? $product['stack_count'] ?? 0;

        if ($stack_count == 0 && $count_selected_limit == 0) {
            return 10;
        }

        // dd($count_selected_limit);

        if (Cart::where('customer_id', $data['customer_id'])->where('product_id', $data['product_id'])->exists()) {
            $cart = Cart::where('customer_id', $data['customer_id'])->where('product_id', $data['product_id'])->first();

            if ($data['increment_decrement'] > 0 && $stack_count <= $cart->count_selected || $data['increment_decrement'] > 0 && $count_selected_limit <= $cart->count_selected) {
                return 30;
            }

            if ($cart->count_selected > 1 && $data['increment_decrement'] <= 0 && $stack_count <= $cart->count_selected && abs($stack_count - $cart->count_selected) > 2) {

                unset($data['increment_decrement']);
                $data['count_selected'] = $stack_count;
                try {
                    if ($stack_count == 0) {
                        $cart->forcedelete();
                    } else {
                        $cart->update($data);
                    }

                    return 40;
                } catch (\Throwable $th) {
                    return 50;
                }

            }

            // if ($cart->count_selected > 1 && $data['increment_decrement'] <= 0 && $count_selected_limit <= $cart->count_selected && abs($stack_count - $cart->count_selected) > 2) {

            //     unset($data['increment_decrement']);
            //     $data['count_selected'] = $count_selected_limit;
            //     try {
            //         if ($stack_count == 0) {
            //             $cart->forcedelete();
            //         } else {
            //             $cart->update($data);
            //         }

            //         return 70;
            //     } catch (\Throwable $th) {
            //         return 80;
            //     }

            // }

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
        $data['customer_id'] = intval($input['customer_id']);

        try {
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

            // dd($data['customer_id'] , $data['product_id'][$i]);

            if ($stack_count > 0) {
                if (NextCart::where('customer_id', $data['customer_id'])->where('product_id', $data['product_id'][$i])->exists()) {
                    continue;
                }

                if (Cart::where('customer_id', $data['customer_id'])->where('product_id', $data['product_id'][$i])->exists()) {
                    $cart = Cart::where('customer_id', $data['customer_id'])->where('product_id', $data['product_id'][$i])->first();

                    if ($cart->count_selected + $data['count_selected'][$i] > $count_selected_limit) {
                        $data['count_selected'][$i] = $count_selected_limit;
                    } else {
                        $data['count_selected'][$i] = $cart->count_selected + $data['count_selected'][$i];
                    }

                    try {
                        $a = array();
                        $a['customer_id'] = $data['customer_id'];
                        $a['product_id'] = $data['product_id'][$i];
                        $a['count_selected'] = $data['count_selected'][$i];
                        $cart->update($a);
                        continue;
                    } catch (\Throwable $th) {
                        continue;
                    }

                } else {

                    if ($data['count_selected'][$i] > $count_selected_limit) {
                        $data['count_selected'][$i] = $count_selected_limit;
                    }

                    try {
                        $a = array();
                        $a['customer_id'] = $data['customer_id'];
                        $a['product_id'] = $data['product_id'][$i];
                        $a['count_selected'] = $data['count_selected'][$i];
                        $a['sale_price'] = $data['sale_price'][$i];
                        $a['discount_price'] = $data['discount_price'][$i];
                        Cart::create($a);
                        continue;
                    } catch (\Throwable $th) {
                        continue;
                    }
                }
            } else {
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
        }
        return 1;
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
