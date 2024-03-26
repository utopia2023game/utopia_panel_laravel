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
                Helper::updatingProductsPrice($product);

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
                return 0 . $th;
            }
        }

        return 0;

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
