<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\ModelsManagement\Category;
use App\Models\AlarmSmartOfferCategory;
use App\Models\AlarmSmartOfferCustomer;
use App\Models\AlarmSmartOfferProduct;
use App\Models\Media;
use App\Models\Product;
use Illuminate\Http\Request;

class AlarmSmartOfferCustomerController extends Controller
{
    public function getOfferCustomerInformation()
    {
        $input = Request()->all();

        Helper::DBConnection(env('SERVER_STATUS', '') . 'utopia_store_' . $input['idb']);

        $Data = array();

        $Data = AlarmSmartOfferCustomer::where('id', $input['alarm_smart_offer_customers_id'])->first()->toArray();

        for ($i = 0; $i < 3; $i++) {
            $loopTimes = $i == 0 ? 'one' : ($i == 1 ? 'two' : 'tree');
            $Data['product_id_' . $loopTimes] = $this->getProductData($Data['as_product_id_' . $loopTimes]);
        }

        for ($i = 0; $i < 3; $i++) {
            $loopTimes = $i == 0 ? 'one' : ($i == 1 ? 'two' : 'tree');
            $Data['category_id_' . $loopTimes] = $this->getCategoryData($Data['as_category_id_' . $loopTimes]);
        }

        return $Data;
    }

    public function getProductData($as_product_id)
    {
        $product_data = AlarmSmartOfferProduct::where('id', $as_product_id)->first()->toArray();

        $product_data['category_name'] = Category::select('name')->where('id', $product_data['category_id'])->first()['name'];

        $product = Product::where('id', $product_data['product_id'])->first();
        $product_data['product_name'] = $product->title;
        $product_data['product_sale_price'] = $product->sale_price;

        $a = Media::where('product_id', $product_data['product_id'])->where('priority', 1)->where('type', 'image')->first();
        if ($a == null) {
            $a = Media::where('product_id', $product_data['product_id'])->where('type', 'image')->first();
        }
        $product_data['product_thumbnail'] = $a == null ? "" : $a['path'];

        return $product_data;
    }

    public function getCategoryData($as_category_id)
    {
        $category_data = AlarmSmartOfferCategory::where('id', $as_category_id)->first()->toArray();

        // $category =  Category::where('id' , $category_data['category_id'])->first();
        $category = Category::where('id', $category_data['category_id'])->with('children')->get()->toArray();

        // dd($category_data['category_id'], $category[0]['children']);

        $category_data['category_name'] = $category[0]['name'];
        $category_data['category_thumbnails'] = array();

        $categoryChildren = $category[0]['children'];
        if (count($categoryChildren) > 0) {
            for ($i = 0; $i < count($categoryChildren); $i++) {
                $thumbnail = $categoryChildren[$i]['image_path'] ?? '';

                if ($thumbnail == '') {
                    continue;
                }
                array_push($category_data['category_thumbnails'], $thumbnail);

                if ($i == 2) {
                    break;
                }
            }
        }

        if (count($category_data['category_thumbnails']) == 0) {
            $thumbnail = $category[0]['image_path'] ?? '';
            if ($thumbnail != '') {
                array_push($category_data['category_thumbnails'], $thumbnail);
            }
        }

        return $category_data;
    }
}
