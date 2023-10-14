<?php

namespace App\Http\Controllers;

use PDO;
use App\Models\User;
use App\Models\Store;
use App\Models\Mobile;
use App\Helpers\Helper;
use App\Models\SmsLogon;
use Illuminate\Http\Request;
use App\Models\CategoryStore;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function loginMobilePassword(Request $request)
    {
        $input = $request->all();

        $mobiles = Mobile::all()->where('mobile', $input['mobile']);

        if ($mobiles == '[]') {
            return 0;
        }

        if (count($mobiles) == 1) {
            $Store = Store::find($mobiles->first()->store_id);

            $dbName = $Store->db_name;

            $storeName = $Store->store_name;
            $storeStatus = $Store->status;
            $storelogo = $Store->logo;
            $storeCategory = CategoryStore::find($Store->category_id)->name;

            Helper::DBConnection(env('SERVER_STATUS' , '') . 'utopia_store_' . $dbName);

            $user = User::all()->where('mobile', $input['mobile'])->first();

            if (Hash::check($input['password'], $user->password)) {

                $a = array();

                $a[0]['id'] = $dbName;
                $a[0]['password'] = true;
                $a[0]['store_category'] = $storeCategory;
                $a[0]['store_name'] = $storeName;
                $a[0]['store_status'] = $storeStatus;
                $a[0]['store_logo'] = $storelogo;
                $user->update(['remember_token' => $input['remember_token']]);

                return $a;
            }
        } else {
            $a = array();
            $pass = false;
            for ($i = 0; $i < count($mobiles); $i++) {
                Helper::DBConnection(env('SERVER_STATUS_PROVIDER' , '') . '0_utopia_management');

                $id = $mobiles[$i]['store_id'];

                $Store = Store::find($id);
                $dbName = $Store->db_name;

                $storeName = $Store->store_name;
                $storeStatus = $Store->status;
                $storelogo = $Store->logo;
                $storeCategory = CategoryStore::find($Store->category_id)->name;

                Helper::DBConnection(env('SERVER_STATUS' , '') . 'utopia_store_' . $dbName);

                $user = User::all()->where('mobile', $input['mobile'])->first();

                if (Hash::check($input['password'], $user->password)) {
                    $a[$i]['id'] = $dbName;
                    $a[$i]['password'] = true;
                    $a[$i]['store_category'] = $storeCategory;
                    $a[$i]['store_name'] = $storeName;
                    $a[$i]['store_status'] = $storeStatus;
                    $a[$i]['store_logo'] = $storelogo;
                    $pass = true;
                    $user->update(['remember_token' => $input['remember_token']]);
                } else {
                    $a[$i]['id'] = $dbName;
                    $a[$i]['password'] = false;
                    $a[$i]['store_category'] = $storeCategory;
                    $a[$i]['store_name'] = $storeName;
                    $a[$i]['store_status'] = $storeStatus;
                    $a[$i]['store_logo'] = $storelogo;
                }
            }
            return $pass ? $a : 0;
        }

        return 0;
    }

    public function loginMobileOverViewPasswordIdb(Request $request)
    {
        $input = $request->all();

        Helper::DBConnection(env('SERVER_STATUS' , '') . 'utopia_store_' . $input['idb']);

        $user = User::all()->where('mobile', $input['mobile'])->first();

        if ($user!='' && Hash::check($input['password'], $user->password)) {
            $user->update(['remember_token' => $input['remember_token']]);
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
    public function createNewStoreWithDbAndMigrate(Request $request)
    {
        $input = $request->all();
        // return  $input;

        $SmsLogon = SmsLogon::all()->where('mobile', $input['mobile'])->first();

        if ($SmsLogon == '') {
            return 0;
        }

        $updatedAt = $SmsLogon->updated_at;

        if (now()->diffInMinutes($updatedAt) >= 2) {
            return 0;
        }

        if (Hash::check($input['mobile_verify_code'], $SmsLogon->code)) {


            //dublicate validation
            $store = Store::all()->where('store_name', $input['store_name']);

            if ($store != '[]') {
                $store = $store->where('mobile', $input['mobile']);
                // return $store->first();
                if ($store == '[]') {
                    return 0;
                } else {
                    if (count($store) == 1) {
                        if ($store->first()->category_id == $input['category_id']) {
                            return 0;
                        }
                    } else {
                        for ($i = 0; $i < count($store); $i++) {
                            if ($store[$i]['category_id'] == $input['category_id']) {
                                return 0;
                            }
                        }
                    }

                }
            }
            //dublicate validation


            try {
                $users = Store::create([
                    'category_id' => $input['category_id'],
                    'store_name' => $input['store_name'],
                    'mobile' => $input['mobile'],
                ]);
            } catch (\Exception $e) {
                return $e->getCode();
            }
            Mobile::create([
                'store_id' => $users->id,
                'mobile' => $input['mobile'],
            ]);

            $dbName = Helper::back4Digits($users->id);

            DB::statement('CREATE DATABASE IF NOT EXISTS ' . env('SERVER_STATUS' , '') . 'utopia_store_' . $dbName);

            DB::table('stores')->where('id', $users->id)->update(['db_name' => $dbName]);

            Helper::DBConnection(env('SERVER_STATUS' , '') . 'utopia_store_' . $dbName);

            Artisan::call('migrate --database=' . env('SERVER_STATUS' , '') . 'utopia_store_' . $dbName);

            User::create([
                'status' => '1',
                'name_family' => $input['name_family'],
                'mobile' => $input['mobile'],
                'mobile_verified_at' => now(),
                'email' => $input['email'],
                'password' => $input['password'],
                'accessibility' => 'boss',
            ]);

            return 1;
        } else {
            return 0;
        }

    }
    public function checkStoreAndSendMobileVerifyCode(Request $request)
    {
        $input = $request->all();
        // return $input;

        $store = Store::all()->where('store_name', $input['store_name']);

        // return $store;
        if ($store == '[]') {
            return Helper::sendSmsVerification($input);
        } else {
            $store = $store->where('mobile', $input['mobile']);
            // return $store;
            if ($store == '[]') {
                return 0;
            } else {
                if (count($store) == 1) {
                    if ($store->first()->category_id == $input['category_id']) {
                        return 0;
                    } else {
                        return Helper::sendSmsVerification($input);
                    }
                } else {
                    for ($i = 0; $i < count($store); $i++) {
                        if ($store[$i]['category_id'] == $input['category_id']) {
                            return 0;
                        }
                    }
                    return Helper::sendSmsVerification($input);
                }
            }
        }
    }

    public function migrateByDataBaseName(Request $request)
    {
        // dd($request);
        Helper::DBConnection(env('SERVER_STATUS' , '') . 'utopia_store_' . $request->dbName);

        Artisan::call('migrate --database=' . env('SERVER_STATUS' , '') . 'utopia_store_' . $request->dbName);

    }

    public function migrateAllDataBase(Request $request)
    {
        $databases = DB::select('SHOW DATABASES');
        foreach ($databases as $database) {
            if (str_contains($database->Database, $request->contains)) {
                Helper::DBConnection($database->Database);
                Artisan::call('migrate --database=' . $database->Database);
            }
        }
    }


    public function rollBackByDataBaseName(Request $request)
    {
        Helper::DBConnection(env('SERVER_STATUS' , '') . 'utopia_store_' . $request->dbName);

        Artisan::call('migrate:rollback --database=' . env('SERVER_STATUS' , '') . 'utopia_store_' . $request->dbName);
    }

    public function rollBackAllDataBase(Request $request)
    {
        $databases = DB::select('SHOW DATABASES');
        foreach ($databases as $database) {
            if (str_contains($database->Database, $request->contains)) {
                Helper::DBConnection($database->Database);
                Artisan::call('migrate:rollback --database=' . $database->Database);
            }
        }
    }


    public function migrateFreshByDataBaseName(Request $request)
    {
        Helper::DBConnection(env('SERVER_STATUS' , '') . 'utopia_store_' . $request->dbName);

        Artisan::call('migrate:fresh --database=' . env('SERVER_STATUS' , '') . 'utopia_store_' . $request->dbName);
    }

    public function migrateFreshAllDataBase(Request $request)
    {
        $databases = DB::select('SHOW DATABASES');
        foreach ($databases as $database) {
            if (str_contains($database->Database, $request->contains)) {
                Helper::DBConnection($database->Database);
                Artisan::call('migrate:fresh --database=' . $database->Database);
            }
        }
    }

    public function categoryStore()
    {
        $categories = CategoryStore::all();

        return $categories;
    }

}