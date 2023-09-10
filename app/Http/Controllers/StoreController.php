<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Store;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Models\CategoryStore;

class StoreController extends Controller
{
    public function storeList(Request $request)
    {
        $input = $request->all();

        $mobile = $input['mobile'];
        $data = $input['data'];

        if ($mobile == '' || $data == '[]') {
            return 0;
        }


        $a = array();
        for ($i = 0; $i < count($data); $i++) {
            Helper::DBConnection('0_utopia_management');

            $dbName = $data[$i]['id'];

            $Store = Store::all()->where('db_name', $dbName)->first();
            $storeName = $Store->store_name;
            $storeStatus = $Store->status;
            $storelogo = $Store->logo;
            // echo $Store->category_id;
            $storeCategory = CategoryStore::find($Store->category_id)->name;
            // echo $storeCategory;

            Helper::DBConnection('utopia_store_' . $dbName);

            $user = User::all()->where('mobile', $mobile )->first();

            $a[$i]['id'] = $dbName;
            $a[$i]['store_category'] = $storeCategory;
            $a[$i]['store_name'] = $storeName;
            $a[$i]['store_status'] = $storeStatus;
            $a[$i]['store_logo'] = $storelogo;
            $a[$i]['status'] = $user->status;
            $a[$i]['accessibility'] = $user->accessibility;

        }
        return $a;


    }
}