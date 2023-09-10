<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\SmsCustomerLogon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CustomerController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests , SoftDeletes;

    // public function loginMobilePassword(Request $request)
    // {
    //     $input = $request->all();

    //     $mobiles = Mobile::all()->where('mobile', $input['mobile']);

    //     if ($mobiles == '[]') {
    //         return 0;
    //     }

    //     if (count($mobiles) == 1) {
    //         $Store = Store::find($mobiles->first()->store_id);

    //         $dbName = $Store->db_name;

    //         $storeName = $Store->store_name;
    //         $storeStatus = $Store->status;
    //         $storelogo = $Store->logo;
    //         $storeCategory = CategoryStore::find($Store->category_id)->name;

    //         Helper::DBConnection('utopia_store_' . $dbName);

    //         $user = User::all()->where('mobile', $input['mobile'])->first();

    //         if (Hash::check($input['password'], $user->password)) {

    //             $a = array();

    //             $a[0]['id'] = $dbName;
    //             $a[0]['password'] = true;
    //             $a[0]['store_category'] = $storeCategory;
    //             $a[0]['store_name'] = $storeName;
    //             $a[0]['store_status'] = $storeStatus;
    //             $a[0]['store_logo'] = $storelogo;
    //             $user->update(['remember_token' => $input['remember_token']]);

    //             return $a;
    //         }
    //     } else {
    //         $a = array();
    //         $pass = false;
    //         for ($i = 0; $i < count($mobiles); $i++) {
    //             Helper::DBConnection('0_utopia_management');

    //             $id = $mobiles[$i]['store_id'];

    //             $Store = Store::find($id);
    //             $dbName = $Store->db_name;

    //             $storeName = $Store->store_name;
    //             $storeStatus = $Store->status;
    //             $storelogo = $Store->logo;
    //             $storeCategory = CategoryStore::find($Store->category_id)->name;

    //             Helper::DBConnection('utopia_store_' . $dbName);

    //             $user = User::all()->where('mobile', $input['mobile'])->first();

    //             if (Hash::check($input['password'], $user->password)) {
    //                 $a[$i]['id'] = $dbName;
    //                 $a[$i]['password'] = true;
    //                 $a[$i]['store_category'] = $storeCategory;
    //                 $a[$i]['store_name'] = $storeName;
    //                 $a[$i]['store_status'] = $storeStatus;
    //                 $a[$i]['store_logo'] = $storelogo;
    //                 $pass = true;
    //                 $user->update(['remember_token' => $input['remember_token']]);
    //             } else {
    //                 $a[$i]['id'] = $dbName;
    //                 $a[$i]['password'] = false;
    //                 $a[$i]['store_category'] = $storeCategory;
    //                 $a[$i]['store_name'] = $storeName;
    //                 $a[$i]['store_status'] = $storeStatus;
    //                 $a[$i]['store_logo'] = $storelogo;
    //             }
    //         }
    //         return $pass ? $a : 0;
    //     }

    //     return 0;
    // }

    public function loginMobilePassword(Request $request)
    {
        $input = $request->all();
        // return $input;

        Helper::DBConnection('utopia_store_' . $input['idb']);

        $customer = Customer::where('mobile', $input['mobile'])->first();

        if ($customer!='' && Hash::check($input['password'], $customer->password)) {
            $customer->update(['remember_token' => $input['remember_token']]);
            return 1;
        }else{
            return 0;
        }

    }

    public function loginMobileSendCode()
    {

    }

    public function loginMobileVerifyCode()
    {

    }
    public function createNewCustomer(Request $request)
    {
        $input = $request->all();
        // return $input;
        
        Helper::DBConnection('utopia_store_' . $input['idb']);

        $SmsCustomerLogon = SmsCustomerLogon::where('mobile', $input['mobile'])->first();

        if ($SmsCustomerLogon == '') {
            return -1;
        }

        $updatedAt = $SmsCustomerLogon->updated_at;

        if (now()->diffInMinutes($updatedAt) >= 2) {
            return -2;
        }

        if (Hash::check($input['mobile_verify_code'], $SmsCustomerLogon->code)) {
            try {
                Customer::create([
                    'status' => '1',
                    'name' => $input['name'],
                    'family' => $input['family'],
                    'mobile' => $input['mobile'],
                    'mobile_verified_at' => now(),
                    'email' => $input['email'],
                    'password' => $input['password'],
                ]);
            } catch (\Exception $e) {
                return $e->getCode();
            }
            return 1;
        } else {
            return 0;
        }

    }

    public function checkCustomerAndSendMobileVerifyCode(Request $request)
    {
        $input = $request->all();
        // return $input;
        Helper::DBConnection('utopia_store_' . $input['idb']);

        $customer = Customer::where('mobile', $input['mobile'])->get();

        // return $customer;
        if ($customer == '[]') {
            return Helper::sendSmsVerificationCustomer($input);
        } else {
            return 0;
        }
    }
}

