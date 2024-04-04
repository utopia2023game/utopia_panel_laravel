<?php

namespace App\Http\ControllersManagement;

use App\Helpers\Helper;
use App\Models\AlarmSmartCategory;
use App\Models\AlarmStatus;
use App\Models\Bank;
use App\Models\Delivery;
use App\Models\DiscountStatus;
use App\Models\OrderStatus;
use App\Models\StackStatus;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    public function createNewManagementDbAndMigrate(Request $request)
    {
        try {
            DB::statement('CREATE DATABASE IF NOT EXISTS ' . env('SERVER_STATUS', '') . $request->dbName);

            Helper::DBConnection(env('SERVER_STATUS', '') . $request->dbName);

            Artisan::call('migrate  --path=/database/migrations/management --database=' . env('SERVER_STATUS', '') . $request->dbName);

            Helper::DBConnection('mysql');
        } catch (\Exception $e) {
            // return $e->getCode();
            return 0;
        }

        // dd($request->dbName);

        return 1;
    }

    public function migrateManagementByDataBaseName(Request $request)
    {
        // dd($request);
        Helper::DBConnection(env('SERVER_STATUS', '') . $request->dbName);

        Artisan::call('migrate --path=/database/migrations/management --database=' . env('SERVER_STATUS', '') . $request->dbName);

    }

    public function transferDataFromMangmentToCustomer(Request $request)
    {
        // dd($request);
        Helper::DBConnection(env('SERVER_STATUS', '') . '0_utopia_management');

        $alarmSmartCategory = AlarmSmartCategory::get()->toArray();
        $alarmStatus = AlarmStatus::get()->toArray();
        $bank = Bank::get()->toArray();
        $delivery = Delivery::get()->toArray();
        $discountStatus = DiscountStatus::get()->toArray();
        $orderStatus = OrderStatus::get()->toArray();
        $stackStatus = StackStatus::get()->toArray();

        // dd($alarmSmartCategory, $alarmStatus, $bank, $delivery, $discountStatus, $orderStatus, $stackStatus);

        Helper::DBConnection(env('SERVER_STATUS', '') . $request->dbName);

        Schema::disableForeignKeyConstraints();

        try {
            // dd($alarmSmartCategory , gettype($alarmSmartCategory));

            AlarmSmartCategory::truncate();
            for ($i = 0; $i < count($alarmSmartCategory); $i++) {
                AlarmSmartCategory::create($alarmSmartCategory[$i]);
            }

        } catch (\Throwable $th) {
            throw $th;
        }
        try {
            AlarmStatus::truncate();
            for ($i = 0; $i < count($alarmStatus); $i++) {
                AlarmStatus::create($alarmStatus[$i]);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
        try {
            Bank::truncate();
            for ($i = 0; $i < count($bank); $i++) {
                Bank::create($bank[$i]);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
        try {
            Delivery::truncate();
            for ($i = 0; $i < count($delivery); $i++) {
                Delivery::create($delivery[$i]);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
        try {
            DiscountStatus::truncate();
            for ($i = 0; $i < count($discountStatus); $i++) {
                DiscountStatus::create($discountStatus[$i]);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
        try {
            OrderStatus::truncate();
            for ($i = 0; $i < count($orderStatus); $i++) {
                OrderStatus::create($orderStatus[$i]);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
        try {
            StackStatus::truncate();
            for ($i = 0; $i < count($stackStatus); $i++) {
                StackStatus::create($stackStatus[$i]);
            }
        } catch (\Throwable $th) {
            throw $th;
        }

        Schema::enableForeignKeyConstraints();

    }

    public function inputCheck(Request $request)
    {
        dd($request);
    }

    // public function migrateByDataBaseName(Request $request)
    // {
    //     // dd($request);
    //     Helper::DBConnection($request->dbName);

    //     Artisan::call('migrate --database=' . env('SERVER_STATUS' , '') . 'utopia_store_' . $request->dbName);

    // }

    //     public function migrateAllDataBase(Request $request)
//     {
//         $databases = DB::select('SHOW DATABASES');
//         foreach ($databases as $database) {
//             if(str_contains($database->Database , $request->contains)){
//                 Helper::DBConnection($database->Database);
//                 Artisan::call('migrate --database=' . $database->Database);
//             }
//         }
//     }

    //     public function rollBackByDataBaseName(Request $request){
//         Helper::DBConnection(env('SERVER_STATUS' , '') . 'utopia_store_' . $request->dbName);

    //         Artisan::call('migrate:rollback --database=' . env('SERVER_STATUS' , '') . 'utopia_store_'.$request->dbName);
//     }

    //     public function rollBackAllDataBase(Request $request){
//         $databases = DB::select('SHOW DATABASES');
//         foreach ($databases as $database) {
//             if(str_contains($database->Database , $request->contains)){
//                 Helper::DBConnection($database->Database);
//                 Artisan::call('migrate:rollback --database=' . $database->Database);
//             }
//         }
//     }

    //     public function migrateFreshByDataBaseName(Request $request){
//         Helper::DBConnection(env('SERVER_STATUS' , '') . 'utopia_store_' . $request->dbName);

    //         Artisan::call('migrate:fresh --database=' . env('SERVER_STATUS' , '') . 'utopia_store_'.$request->dbName);
//     }

    //     public function migrateFreshAllDataBase(Request $request){
//         $databases = DB::select('SHOW DATABASES');
//         foreach ($databases as $database) {
//             if(str_contains($database->Database , $request->contains)){
//                 Helper::DBConnection($database->Database);
//                 Artisan::call('migrate:fresh --database=' . $database->Database);
//             }
//         }
//     }

}
