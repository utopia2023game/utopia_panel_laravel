<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\ModelsManagement\Category;
use App\Models\AlarmSmartOfferCategory;
use App\Models\AlarmSmartOfferCustomer;
use App\Models\AlarmSmartOfferProduct;
use App\Models\AlarmStatus;
use App\Models\DiscountStatus;
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

        $AlarmStatus = AlarmStatus::get();
        $Data['alarm_status_name'] = $AlarmStatus->where('id', $Data['alarm_status_id'])->first()->name_fa;

        $DiscountStatus = DiscountStatus::get();
        $Data['setting_discount_status_name'] = $DiscountStatus->where('id', $Data['setting_discount_status_id'])->first()->name_fa;

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

    public function setAlarmSmartOfferProductCategoryStatus()
    {
        $input = Request()->all();

        Helper::DBConnection(env('SERVER_STATUS', '') . 'utopia_store_' . $input['idb']);

        $indexName = $input['index'] == 0 ? 'one' : ($input['index'] == 1 ? 'two' : 'tree');
        $productCategory = $input['product_category'] == 0 ? 'product' : ($input['product_category'] == 1 ? 'category' : 0);

        if ($productCategory == 0) {
            return 0;
        }
        $a = array();
        $a[$productCategory . '_id_' . $indexName . '_status'] = $input['status'];

        $result = AlarmSmartOfferCustomer::where('id', $input['alarm_smart_offer_customers_id'])->update($a);

        if ($result == 0) {
            return 0;
        }

        $AlarmSmartOfferCustomer = AlarmSmartOfferCustomer::where('id', $input['alarm_smart_offer_customers_id'])->first()->toArray();
        $count = 0;
        for ($i = 0; $i < 3; $i++) {
            $indexName = $i == 0 ? 'one' : ($i == 1 ? 'two' : 'tree');
            if ($AlarmSmartOfferCustomer[$productCategory . '_id_' . $indexName . '_status'] == 1) {
                $count++;
            }
        }

        $b = array();
        $b['setting_' . $productCategory . '_count'] = $count;
        $result = AlarmSmartOfferCustomer::where('id', $input['alarm_smart_offer_customers_id'])->update($b);
        return $result;
    }
}
