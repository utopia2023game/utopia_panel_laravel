<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Media;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function create(Request $request)
    {
        $input = $request->all();

        Helper::DBConnection(env('SERVER_STATUS', '') . 'utopia_store_' . $input['idb']);

        $request->validate([
            'name' => 'required',
            'parent_id' => 'nullable|numeric',
        ]);

        if (Category::where('id', $input['parent_id'])->exists()) {
            Category::create([
                'name' => $input['name'],
                'parent_id' => $input['parent_id'],
            ]);
            return 1;
        } else if ($input['parent_id'] == '') {
            Category::create([
                'name' => $input['name'],
            ]);
            return 1;
        } else {
            return 0;
        }
    }

    public function update(Request $request)
    {
        $input = $request->all();

        Helper::DBConnection(env('SERVER_STATUS', '') . 'utopia_store_' . $input['idb']);

        $request->validate([
            'name' => 'required',
            'id' => 'nullable|numeric',
        ]);

        $result = Category::where('id', $input['id'])->update([
            'name' => $request->name,
        ]);

        return $result;
    }

    public function restore(Request $request)
    {
        $input = $request->all();

        Helper::DBConnection(env('SERVER_STATUS', '') . 'utopia_store_' . $input['idb']);

        $request->validate([
            'id' => 'nullable|numeric',
        ]);

        $result = Category::where('id', $input['id'])->restore();

        return $result;
    }
    public function softdelete(Request $request)
    {

        $input = $request->all();

        Helper::DBConnection(env('SERVER_STATUS', '') . 'utopia_store_' . $input['idb']);

        $request->validate([
            'id' => 'nullable|numeric',
        ]);

        if (Category::where('id', $input['id'])->exists()) {
            $categories = Category::where('id', $input['id'])->with('children')->get();

            if (count($categories[0]->children) == 0) {
                Category::where('id', $input['id'])->delete();
                return 1;
            } else {
                return count($categories[0]->children);
            }
        } else {
            return 0;
        }

    }

    public function forcedelete(Request $request)
    {
        $input = $request->all();

        Helper::DBConnection(env('SERVER_STATUS', '') . 'utopia_store_' . $input['idb']);

        $request->validate([
            'id' => 'nullable|numeric',
        ]);

        if (Category::where('id', $input['id'])->exists()) {
            $categories = Category::where('id', $input['id'])->with('children')->get();

            if (count($categories[0]->children) == 0) {
                Category::where('id', $input['id'])->forcedelete();
                return 1;
            } else {
                return count($categories[0]->children);
            }
        } else {
            return 0;
        }

    }

    public function categoryAddRemoveImage(Request $request)
    {
        $input = $request->all();

        Helper::DBConnection(env('SERVER_STATUS', '') . 'utopia_store_' . $input['idb']);

        $request->validate([
            'id' => 'nullable|numeric',
        ]);

        if (Category::where('id', $input['id'])->exists()) {
            $category_data = Category::where('id', $input['id']);
            if ($input['status'] == 'add') {
                if ($request->hasFile('image_file')) {
                    $file_name = rand(1000, 9999) . '_' . $input['image_file']->getClientOriginalName();
                    // $destinationPath = 'uploads/' . $type . '/' . $file_name;
                    $destinationPath = 'uploads/' . $input['idb'] . '/categories_image/';
                    $input['image_file']->move($destinationPath, $file_name);
                    $data['image_path'] = $destinationPath . $file_name;
                    try {
                        $result = $category_data->update($data);
                        if ($result > 0) {
                            return $data['image_path'];
                        }
                    } catch (Exception $e) {
                        return 0;
                    }

                } else {
                    return 0;
                }

            } else if ($input['status'] == 'remove') {
                $category_data = $category_data->first();
                $image_path = $category_data['image_path'] == null ? '' : $category_data['image_path'];
                try {
                    if ($image_path != '' && file_exists(public_path($image_path))) {
                        unlink(public_path($image_path));
                    }
                    $data['image_path'] = '';
                    $result = $category_data->update($data);
                    if ($result > 0) {
                        return '';
                    }
                } catch (Exception $e) {
                    return 0;
                }
            }

        } else {
            return 0;
        }

    }

    public function listCategory(Request $request)
    {
        $input = $request->all();

        Helper::DBConnection(env('SERVER_STATUS', '') . 'utopia_store_' . $input['idb']);

        if ($input['children'] == 'true') {
            $categories = Category::whereNull('parent_id')->with('children')->get();
        } else {
            $categories = Category::whereNull('parent_id')->cursorPaginate(10)->items();
        }

        return $categories;
    }

    public function productListByCategoryId(Request $request)
    {
        $input = $request->all();

        // return $input;

        Helper::DBConnection(env('SERVER_STATUS', '') . 'utopia_store_' . $input['idb']);

        $customer_id = $input['customer_id'];
        $search = $input['search'];
        $filter = $input['filter'];
        $hashtag = $input['hashtag'];
        $sort = $input['sort'];

        $products = array();

        $data_by_cat_id = $input['data_by_cat_id'];

        if ($data_by_cat_id == 0) {
            $items[0][0] = array();
            array_push($items[0][0], 'همه دسته ها');

            $items[0][1] = array();
            array_push($items[0][1], 0);

            $items[1] = Category::whereNull('parent_id')->get();
        } else {
            $items[0][0] = explode('/', $this->getParentNameCategoriesById($data_by_cat_id));

            $data_by_cat_id = $this->getParentIdCategoriesById($data_by_cat_id);

            $items[0][1] = json_decode($data_by_cat_id);

            $items[1] = Category::where('parent_id', $input['data_by_cat_id'])->get();
        }

        if (!empty($search) && !empty($sort)) {
            if ($sort == 'popular') {
                $products = Product::where('title', 'LIKE', '%' . $search . '%')->orderBy('page_view', 'desc')->get();
            } else if ($sort == 'newest') {
                $products = Product::where('title', 'LIKE', '%' . $search . '%')->orderBy('created_at', 'desc')->get();
            } else if ($sort == 'expensive') {
                $products = Product::where('title', 'LIKE', '%' . $search . '%')->orderByRaw('(sale_price - discount_price) DESC')->get();
            } else if ($sort == 'inexpensive') {
                $products = Product::where('title', 'LIKE', '%' . $search . '%')->orderByRaw('(sale_price - discount_price) ASC')->get();
            } else if ($sort == 'best_selling') {
                $products = Product::where('title', 'LIKE', '%' . $search . '%')->get(); // add order count to table and when set
            } else {
                $products = Product::where('title', 'LIKE', '%' . $search . '%')->get();
            }
        } else if (empty($search) && !empty($sort)) {
            if ($sort == 'popular') {
                $products = Product::orderBy('page_view', 'desc')->get();
            } else if ($sort == 'newest') {
                $products = Product::orderBy('created_at', 'desc')->get();
            } else if ($sort == 'expensive') {
                $products = Product::orderByRaw('(sale_price - discount_price) DESC')->get();
            } else if ($sort == 'inexpensive') {
                $products = Product::orderByRaw('(sale_price - discount_price) ASC')->get();
            } else if ($sort == 'best_selling') {
                $products = Product::all(); // add order count to table and when set
            } else {
                $products = Product::all();
            }
        } else if (!empty($search) && empty($sort)) {
            $products = Product::where('title', 'LIKE', '%' . $search . '%')->get();
        } else if (empty($search) && empty($sort)) {
            $products = Product::orderBy('created_at', 'desc')->get();
        }

        $items[2] = array();
        for ($i = 0; $i < count($products); $i++) {
            $result_time = Helper::updatingProductsPrice($products[$i]);

            if (!$result_time) {
                $products[$i]['confirm_discount'] = 0;
                $products[$i]['discount_percent'] = 0;
                $products[$i]['discount_manual'] = 0;
                $products[$i]['discount_price'] = 0;
                $products[$i]['discount_time_from'] = '';
                $products[$i]['discount_time_until'] = '';
            }
            // echo $products[$j];
            $categories_id = json_decode($products[$i]->categories_id);
            for ($j = 0; $j < count($categories_id); $j++) {
                $cat_id[$j] = $this->getParentIdCategoriesById($categories_id[$j]);
                if ($j == 0) {
                    $cat_name = array();
                }

                if ($data_by_cat_id == 0 || $this->compareTwoVariable($data_by_cat_id, $cat_id[$j]) == 1) {
                    $cat_name[count($cat_name)] = $this->getCatNameCategoriesById($categories_id[$j]);
                }

                if ($j + 1 == count($categories_id) && count($cat_name) > 0) {
                    # last loop...
                    $products[$i]['categories_name'] = $cat_name;
                    $a = Media::where('product_id', $products[$i]['id'])->where('priority', 1)->where('type', 'image')->first();
                    if ($a == null) {
                        $a = Media::where('product_id', $products[$i]['id'])->where('type', 'image')->first();
                    }
                    $products[$i]['thumbnail_image'] = $a == null ? "" : $a['path'];
                    $products[$i]['count_selected'] = 0;

                    if ($customer_id != 0) {
                        if (Cart::where('customer_id', $customer_id)->where('product_id', $products[$i]['id'])->exists()) {
                            $carts = Cart::where('customer_id', $customer_id)->where('product_id', $products[$i]['id'])->first()->toArray();
                            $products[$i]['count_selected'] = $carts['count_selected'];
                            // dd($carts);
                        }
                    }

                    array_push($items[2], $products[$i]);
                    // $items[2][count($items[2])] = $products[$i];
                }
            }
        }
        return $items;
    }

    public function getParentNameCategoriesById($id)
    {
        $cat = Category::find($id);
        if ($cat->parent_id != null) {
            return $cat->parent->getParentsNamesWithoutSpace() . "/" . $cat->name;
        }
        return $cat->name;
    }

    public function getCatNameCategoriesById($id)
    {
        $cat = Category::find($id);
        return $cat->name;
    }

    public function getParentIdCategoriesById($id)
    {
        $cat = Category::find($id);

        // dd($id,$cat->parent->getParentsIds());
        if ($cat->parent_id != null) {
            return '[' . $cat->parent->getParentsIds() . ',' . $cat->id . ']';
        }
        return '[' . $cat->id . ']';
    }
    public function compareTwoVariable($varOriginal, $varCompare)
    {
        if ($varOriginal === $varCompare) {
            return 1;
        }
        $varOriginal = json_decode($varOriginal);
        $varCompare = json_decode($varCompare);

        // dd($varOriginal ,$varCompare);
        // dd(count($varOriginal) <= count($varCompare));
        if (count($varOriginal) <= count($varCompare)) {
            for ($i = 0; $i < count($varOriginal); $i++) {
                if ($varOriginal[$i] != $varCompare[$i]) {
                    return 0;
                }
            }
        } else {
            return 0;
        }

        // if($var1 === $var2){
        //     return true;
        // }
        return 1;
    }

}
