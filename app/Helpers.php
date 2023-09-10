<?php

namespace App\Helpers;

use PDO;
use App\Models\SmsLogon;
use App\Models\Employees;
use App\Models\SmsCustomerLogon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;

class Helper
{

    public static function sendSmsVerification($input)
    {
        $code = rand(10000, 99999);
        $SmsLogon = SmsLogon::all()->where('mobile', $input['mobile'])->first();
        // return $SmsLogon;
        if ($SmsLogon == '') {
            SmsLogon::create([
                'mobile' => $input['mobile'],
                'code' => Hash::make($code),
            ]);
        } else {
            $SmsLogon->update([
                'code' => Hash::make($code),
                'entry_times' => $SmsLogon['entry_times'] + 1,
            ]);
        }
        return $code;
    }

    public static function sendSmsVerificationCustomer($input)
    {
        $code = rand(10000, 99999);
        $SmsCustomerLogon = SmsCustomerLogon::all()->where('mobile', $input['mobile'])->first();
        // return $SmsLogon;
        if ($SmsCustomerLogon == '') {
            SmsCustomerLogon::create([
                'mobile' => $input['mobile'],
                'code' => Hash::make($code),
            ]);
        } else {
            $SmsCustomerLogon->update([
                'code' => Hash::make($code),
                'entry_times' => $SmsCustomerLogon['entry_times'] + 1,
            ]);
        }
        return $code;
    }

    // public static function getEmployeeStatus($id=0){
    //     $record = Employees::find($id);

    //     return $record->status;
    // }

    public static function DBConnection($dbName)
    {
        if (env('APP_ENV') == 'local') {
            // $dbName = "test_me";
            $host = '127.0.0.1';
            $user = "root";
            $password = "";
        } else {
            // $dbName = 'tic_tac';
            $host = 'localhost';
            $user = "";
            $password = "";
        }
        $state = true;
        try {
            Config::set(
                'database.connections.' . $dbName,
                array(
                    'driver' => 'mysql',
                    'url' => env('DATABASE_URL'),
                    'host' => $host,
                    'port' => env('DB_PORT', '3306'),
                    'database' => $dbName,
                    'username' => $user,
                    'password' => $password,
                    'unix_socket' => env('DB_SOCKET', ''),
                    'charset' => 'utf8',
                    'collation' => 'utf8_unicode_ci',
                    'prefix' => '',
                    'prefix_indexes' => true,
                    'strict' => false,
                    'engine' => null,
                    'options' => extension_loaded('pdo_mysql') ? array_filter([
                        PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
                    ]) : [],
                )
            );
            /* \DB::setDefaultConnection('myConnection');
              $state = \DB::connection()->getPdo();*/

            Config::set('database.connections.' . $dbName . '.database', $dbName);
            DB::setDefaultConnection($dbName);
            DB::reconnect($dbName);

        } catch (\Exception $e) {
            $state = false;
        }
        return $state;
    }


    public static function getDatabaseName()
    {
        $databaseName = DB::connection()->getDatabaseName();

        dd($databaseName);
    }

    public static function getAllDatabaseNameFromServerByContains($contains)
    {
        $databases = DB::select('SHOW DATABASES');
        // dd($databases);
        foreach ($databases as $database) {
            if (str_contains($database->Database, $contains)) {
                echo $database->Database . "\n";
            }
        }
    }

    public static function getAllTablesNameFromDatabase()
    {
        $tables = DB::select('SHOW TABLES');
        foreach ($tables as $table) {
            echo $table->Tables_in_db_name;
        }
    }

    public static function back4Digits($num)
    {
        $num4Digits = $num;
        for ($i = 0; $i < 4 - strlen($num); $i++) {
            $num4Digits = '0' . $num4Digits;
        }
        return $num4Digits;
    }
}