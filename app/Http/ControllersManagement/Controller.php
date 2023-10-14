<?php

namespace App\Http\ControllersManagement;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

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