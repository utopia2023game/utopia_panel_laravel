<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Helpers\Helper;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class CategoryController extends Controller
{
    public function create(Request $request)
    {
        $input = $request->all();

        Helper::DBConnection('utopia_store_' . $input['idb']);

        $request->validate([
            'name' => 'required',
            'parent_id' => 'nullable|numeric'
        ]);

        if (Category::where('id', $input['parent_id'])->exists()) {
            Category::create([
                'name' => $input['name'],
                'parent_id' => $input['parent_id']
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

        Helper::DBConnection('utopia_store_' . $input['idb']);

        $request->validate([
            'name' => 'required',
            'id' => 'nullable|numeric'
        ]);

        $result = Category::where('id', $input['id'])->update([
            'name' => $request->name
        ]);

        return $result;
    }


    public function restore(Request $request)
    {
        $input = $request->all();

        Helper::DBConnection('utopia_store_' . $input['idb']);

        $request->validate([
            'id' => 'nullable|numeric'
        ]);

        $result = Category::where('id', $input['id'])->restore();

        return $result;
    }
    public function softdelete(Request $request)
    {

        $input = $request->all();

        Helper::DBConnection('utopia_store_' . $input['idb']);

        $request->validate([
            'id' => 'nullable|numeric'
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

        Helper::DBConnection('utopia_store_' . $input['idb']);

        $request->validate([
            'id' => 'nullable|numeric'
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

        Helper::DBConnection('utopia_store_' . $input['idb']);

        $request->validate([
            'id' => 'nullable|numeric'
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

        Helper::DBConnection('utopia_store_' . $input['idb']);

        if ($input['children'] == 'true') {
            $categories = Category::whereNull('parent_id')->with('children')->get();
        } else {
            $categories = Category::whereNull('parent_id')->cursorPaginate(10)->items();
        }

        return $categories;
    }

    public function categoryChildrenListByCatId(Request $request)
    {
        $input = $request->all();

        Helper::DBConnection('utopia_store_' . $input['idb']);

        $data_by_cat_id = $input['data_by_cat_id'];

        $items[0][0] = explode('/', $this->getParentNameCategoriesById($data_by_cat_id));

        $data_by_cat_id = $this->getParentIdCategoriesById($data_by_cat_id);

        $items[0][1] = json_decode($data_by_cat_id);

        $items[1] = Category::where('parent_id', $input['data_by_cat_id'])->get();

        $products = Product::orderBy('created_at', 'desc')->get();

        $items[2] = array();
        for ($i = 0; $i < count($products); $i++) {
            Helper::updatingProductsPrice($products[$i]);

            $categories_id = json_decode($products[$i]->categories_id);
            for ($j = 0; $j < count($categories_id); $j++) {
                $cat_id[$j] = $this->getParentIdCategoriesById($categories_id[$j]);
                if ($j == 0) {
                    $cat_name = array();
                }

                if ($this->compareTwoVariable($data_by_cat_id, $cat_id[$j]) == 1) {
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
                    $items[2][count($items[2])] = $products[$i];
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