<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Cart;
use App\Models\Media;
use App\Helpers\Helper;
use App\Models\Comment;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function create(Request $request)
    {
        $input = $request->all();

        // return $input;

        Helper::DBConnection(env('SERVER_STATUS' , '') . 'utopia_store_' . $input['idb']);

        $data = array();

        $data['user_id'] = $input['user_id'];
        $data['categories_id'] = $input['categories_id'];
        $data['title'] = $input['title'];
        $data['html'] = $input['html'];
        $data['ribbon'] = $input['ribbon'];
        $data['weight'] = $input['weight'];
        $data['width'] = $input['width'];
        $data['height'] = $input['height'];
        $data['length'] = $input['length'];
        $data['stack_status'] = $input['stack_status'];
        $data['stack_count'] = $input['stack_count'];
        $data['stack_limit'] = $input['stack_limit'];
        $data['barcode'] = $input['barcode'];
        $data['product_code'] = $input['product_code'];
        $data['sale_price'] = $input['sale_price'];
        $data['purchase_price'] = $input['purchase_price'];
        $data['confirm_discount'] = $input['confirm_discount'];
        $data['discount_percent'] = $input['discount_percent'];
        $data['discount_manual'] = $input['discount_manual'];
        $data['discount_price'] = $input['discount_price'];
        $data['discount_time_from'] = $input['discount_time_from'];
        $data['discount_time_until'] = $input['discount_time_until'];
        $data['safe_discount_percent'] = $input['safe_discount_percent'];
        $data['special_discount_percent'] = $input['special_discount_percent'];
        $data['exceptional_discount_percent'] = $input['exceptional_discount_percent'];
        // return $data;
        try {
            $product_create = Product::create($data);
        } catch (Exception $e) {
            return ($e->getMessage());
        }


        // return '$product_create';
        $product_id = $product_create->id;
        // $product_id = 35 ;

        $images = $input['images'] == "[]" ? [] : $input['images'];
        // dd($images);

        $videos = $input['videos'] == "[]" ? [] : $input['videos'];


        // return $videos ;
        if (count($images) <= 6) {
            $result_images = $this->uploadFiles($images, 'image', $product_id, $input['idb']);
        } else {
            return 0;
        }
        // return $videos ;
        if (count($videos) <= 2) {
            $result_videos = $this->uploadFiles($videos, 'video', $product_id, $input['idb']);
        } else {
            return 0;
        }

        // return $result_videos . ' ' . $result_images;
        if ($result_images == count($images) && $result_videos == count($videos)) {
            return 1;
        } else if ($result_images != count($images) && $result_videos != count($videos)) {
            return 2;
        } else if ($result_images != count($images)) {
            return 3;
        } else if ($result_videos != count($videos)) {
            return 4;
        } else {
            return 0;
        }

    }

    public function uploadFiles($files, $type, $product_id, $idb)
    {
        $result_number = 0;
        foreach ($files as $file) {
            $file_name = rand(1000, 9999) . '_' . $file['detail']->getClientOriginalName();
            // $destinationPath = 'uploads/' . $type . '/' . $file_name;
            $destinationPath = 'uploads/' . $idb . '/' . $type . '/';
            $file['detail']->move($destinationPath, $file_name);

            Media::create([
                'product_id' => $product_id,
                'priority' => $file['priority'],
                'type' => $type,
                'path' => $destinationPath . $file_name,
            ]);
            $result_number++;
        }
        return $result_number;
    }

    public function update(Request $request)
    {
        $input = $request->all();
        $product_id = $input['id'];

        Helper::DBConnection(env('SERVER_STATUS' , '') . 'utopia_store_' . $input['idb']);

        $data = array();

        $data['user_id'] = $input['user_id'];
        $data['categories_id'] = $input['categories_id'];
        $data['title'] = $input['title'];
        $data['html'] = $input['html'];
        $data['ribbon'] = $input['ribbon'];
        $data['weight'] = $input['weight'];
        $data['width'] = $input['width'];
        $data['height'] = $input['height'];
        $data['length'] = $input['length'];
        $data['stack_status'] = $input['stack_status'];
        $data['stack_count'] = $input['stack_count'];
        $data['stack_limit'] = $input['stack_limit'];
        $data['barcode'] = $input['barcode'];
        $data['product_code'] = $input['product_code'];
        $data['sale_price'] = $input['sale_price'];
        $data['purchase_price'] = $input['purchase_price'];
        $data['confirm_discount'] = $input['confirm_discount'];
        $data['discount_percent'] = $input['discount_percent'];
        $data['discount_manual'] = $input['discount_manual'];
        $data['discount_price'] = $input['discount_price'];
        $data['discount_time_from'] = $input['discount_time_from'];
        $data['discount_time_until'] = $input['discount_time_until'];
        $data['safe_discount_percent'] = $input['safe_discount_percent'];
        $data['special_discount_percent'] = $input['special_discount_percent'];
        $data['exceptional_discount_percent'] = $input['exceptional_discount_percent'];
        // return $data;
        try {
            $product_update = Product::where('id', $product_id)->update($data);
        } catch (Exception $e) {
            return ($e->getMessage());
        }


        // return $product_update . "\n";
        // $product_id = 35 ;

        if ($product_update > 0) {



            $images = $input['images'] == "[]" ? [] : $input['images'];
            // dd($images);

            $videos = $input['videos'] == "[]" ? [] : $input['videos'];
            // dd($videos);

            // return $videos ;
            if (count($images) <= 6) {
                $result_images = $this->uploadUpdateFiles($images, 'image', $product_id, $input['idb']);
            } else {
                return 0;
            }
            // return $videos ;
            if (count($videos) <= 2) {
                $result_videos = $this->uploadUpdateFiles($videos, 'video', $product_id, $input['idb']);
            } else {
                return 0;
            }

            // return $result_videos . ' ' . $result_images;
            if ($result_images == count($images) && $result_videos == count($videos)) {
                return 1;
            } else if ($result_images != count($images) && $result_videos != count($videos)) {
                return 2;
            } else if ($result_images != count($images)) {
                return 3;
            } else if ($result_videos != count($videos)) {
                return 4;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
        // return $result;
    }


    public function uploadUpdateFiles($files, $type, $product_id, $idb)
    {
        $mediaTable = Media::where('product_id', $product_id)->where('type', $type);

        // echo $mediaTable->get()[0]['id'] ;
        $mediaData = $mediaTable->get()->toArray();
        $mediaDataTemp = $mediaTable->get()->toArray();
        // dd($files , $mediaDataTemp);
        $result_number = 0;

        if (count($mediaData) > 0 && count($files) != 0) {
            for ($i = 0; $i < count($mediaData); $i++) {
                // echo 'type => ' . $type . ' / mediaData id => ' . $mediaData[$i]['id'] . "\n";
                for ($j = 0; $j < count($files); $j++) {
                    // echo 'type => ' . $type . ' / files id => ' . $files[$j]['id'] . "\n";
                    if (strval($mediaData[$i]['id']) == strval($files[$j]['id'])) {
                        // echo 'type => ' . $type . ' / status true by id => ' . $files[$j]['id'] . "\n";
                        if (!empty($files[$j]['detail'])) {

                            try {
                                $media = $mediaTable->where('id', $files[$j]['id'])->first();
                                $media_path = $media['path'] == null ? '' : $media['path'];
                                if ($media_path != '' && file_exists(public_path($media_path))) {
                                    unlink(public_path($media_path));
                                }
                            } catch (Exception $e) {
                                array_splice($mediaDataTemp, $i, 1); // array remove at
                                array_splice($files, $j, 1); // array remove at
                                $result_number++;
                                break;
                                // return ($e->getMessage());
                            }

                            $file_name = rand(1000, 9999) . '_' . $files[$j]['detail']->getClientOriginalName();
                            // $destinationPath = 'uploads/' . $type . '/' . $file_name;
                            $destinationPath = 'uploads/' . $idb . '/' . $type . '/';
                            $files[$j]['detail']->move($destinationPath, $file_name);

                            $data['product_id'] = $product_id;
                            $data['priority'] = $files[$j]['priority'];
                            $data['type'] = $type;
                            $data['path'] = $destinationPath . $file_name;
                            $res = $media->update($data);
                            if ($res > 0) {
                                array_splice($mediaDataTemp, $i, 1); // array remove at
                                array_splice($files, $j, 1); // array remove at
                                $result_number++;
                                break;
                            }

                        } else {
                            array_splice($mediaDataTemp, $i, 1); // array remove at
                            array_splice($files, $j, 1); // array remove at
                            $result_number++;
                            break;
                        }
                    }
                }
            }
        }

        // dd($mediaData, $mediaDataTemp);
        if (count($mediaDataTemp) > 0) {
            foreach ($mediaDataTemp as $data) {
                try {
                    // dd($data['id']);
                    $media = Media::where('product_id', $product_id)->where('type', $type)->where('id', $data['id'])->first();

                    $media_path = $media['path'] == null ? '' : $media['path'];
                    // dd($media, $data['id'], $media_path);
                    if ($media_path != '' && file_exists(public_path($media_path))) {
                        unlink(public_path($media_path));
                    }
                    $media->restore();

                    if ($media->exists()) {
                        $media->forcedelete();
                    }
                } catch (Exception $e) {
                    break;
                    // return ($e->getMessage());
                }

            }
        }

        if (count($files) > 0) {
            foreach ($files as $file) {
                if (!empty($file['detail'])) {


                    $file_name = rand(1000, 9999) . '_' . $file['detail']->getClientOriginalName();
                    // $destinationPath = 'uploads/' . $type . '/' . $file_name;
                    $destinationPath = 'uploads/' . $idb . '/' . $type . '/';
                    $file['detail']->move($destinationPath, $file_name);

                    $data['product_id'] = $product_id;
                    $data['priority'] = $file['priority'];
                    $data['type'] = $type;
                    $data['path'] = $destinationPath . $file_name;


                    Media::create($data);


                    $result_number++;
                }
            }
        }

        return $result_number;
    }


    public function restore(Request $request)
    {

        $input = $request->all();

        Helper::DBConnection(env('SERVER_STATUS' , '') . 'utopia_store_' . $input['idb']);
        // $request->validate([
        //     'id' => 'nullable|numeric'
        // ]);

        $result = Product::where('id', $request->id)->restore();

        return $result;
    }
    public function softdelete(Request $request)
    {

        $input = $request->all();

        Helper::DBConnection(env('SERVER_STATUS' , '') . 'utopia_store_' . $input['idb']);
        // $request->validate([
        //     'id' => 'nullable|numeric'
        // ]);

        if (Product::where('id', $request->id)->exists()) {
            Product::where('id', $request->id)->delete();
            return 1;
        } else {
            return 0;
        }

    }

    public function forcedelete(Request $request)
    {
        $input = $request->all();

        Helper::DBConnection(env('SERVER_STATUS' , '') . 'utopia_store_' . $input['idb']);

        // $request->validate([
        //     'id' => 'nullable|numeric'
        // ]);

        Product::where('id', $request->id)->restore();

        if (Product::where('id', $request->id)->exists()) {
            Product::where('id', $request->id)->forcedelete();
            return 1;
        } else {
            return 0;
        }

    }


    public function ProductData()
    {
        $input = Request()->all();

        Helper::DBConnection(env('SERVER_STATUS' , '') . 'utopia_store_' . $input['idb']);

        $product = Product::find($input['product_id']);

        Helper::updatingProductsPrice($product);

        // return 'end';

        $categories = json_decode($product->categories_id);
        $a = array();
        for ($i = 0; $i < count($categories); $i++) {
            $name = $this->getParentNameCategoriesById($categories[$i]);

            if ($name != null) {
                $a[$i]['id'] = $categories[$i];
                $a[$i]['name'] = $name;
            }

        }
        $product['categories_id'] = $a;

        $product['files'] = Product::find($input['product_id'])->getMedia;

        $product['comments_data'] = array();
        $b['rate'] = Comment::where('product_id', $input['product_id'])->where('status', 2)->avg('rate');
        $b['comments'] = Comment::where('product_id', $input['product_id'])->where('status', 2)->orderBy('created_at', 'desc')->get();
        $product['comments_data'] = $b;
        $product['count_selected'] = 0;
        if(Cart::where('customer_id', $input['customer_id'])->where('product_id', $input['product_id'])->exists()){
            $product['count_selected'] = Cart::where('customer_id', $input['customer_id'])->where('product_id', $input['product_id'])->get('count_selected')[0]['count_selected'];
        }


        return $product;
    }

    public function getParentNameCategoriesById($id)
    {
        $cat = Category::find($id);
        // dd($cat);
        // return $cat;
        if ($cat == null) {
            return $cat;
        }

        if ($cat->parent_id != null) {
            return $cat->parent->getParentsNames() . "   /   " . $cat->name;
        }
        return $cat->name;
    }

    public function listProducts(Request $request)
    {
        $input = $request->all();

        Helper::DBConnection(env('SERVER_STATUS' , '') . 'utopia_store_' . $input['idb']);

        $products = Product::orderBy('id', 'DESC')->get();

        for ($i = 0; $i < count($products); $i++) {
            Helper::updatingProductsPrice($products[$i]);
            
            $a = Media::where('product_id', $products[$i]['id'])->where('priority', 1)->where('type', 'image')->first();
            if ($a == null) {
                $a = Media::where('product_id', $products[$i]['id'])->where('type', 'image')->first();
            }
            $products[$i]['thumbnail_image'] = $a == null ? "" : $a['path'];
        }


        return $products;
    }

    public function listCarts(Request $request)
    {

        $input = $request->all();

        $ids = json_decode($input['id']);

        Helper::DBConnection(env('SERVER_STATUS' , '') . 'utopia_store_' . $input['idb']);

        $products = array();

        for ($i = 0; $i < count($ids); $i++) {
            $product = Product::find($ids[$i]);

            Helper::updatingProductsPrice($product);
            // echo 'product  => ' . empty($product) . "\n";

            if (!empty($product)) {

                $a = Media::where('product_id', $product['id'])->where('priority', 1)->where('type', 'image')->first();
                if ($a == null) {
                    $a = Media::where('product_id', $product['id'])->where('type', 'image')->first();
                }
                $product['thumbnail_image'] = $a == null ? "" : $a['path'];

                $products[count($products)] = $product;
            }
        }
        return $products;

    }

    public function setpage_viewProduct(Request $request)
    {
        $input = $request->all();

        Helper::DBConnection(env('SERVER_STATUS' , '') . 'utopia_store_' . $input['idb']);

        $product = Product::find($input['id']);

        $product = $product->update([
            'page_view' => ($product->page_view) + 1
        ]);

        return $product;
    }

    // public function search(Request $request){
    //     $categories = Category::all();

    //     $txtSearch = $request->input('q');
    //     if(isset($txtSearch)){
    //        $query = Post::where('title', 'LIKE', "%$txtSearch%")->orderBy('id', 'DESC');
    //     }else{       

    //     $query = Post::orderBy('id', 'DESC');


    //       if ($request->has('cate')) {
    //         $categoryType = $request->input('cate');
    //          foreach ($categoryType as $category) {

    //            $query->where('category_slug', $category);

    //          }
    //       }

    //     }

    //     $queryResults = $query->paginate(20);

    //     // return view('searchPage, ['categories' => $categories, 'queryResults' => $queryResults]);

    //   }
}