<?php

namespace App\Helpers;

use PDO;
use App\Models\SmsLogon;
use App\Models\Employees;
use Hekmatinasser\Verta\Verta;
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


    public static function updatingProductsPrice($product)
    {

        $discount_time_from = $product->discount_time_from == null || $product->discount_time_from == '' ? '' : $product->discount_time_from;
        $discount_time_until = $product->discount_time_until == null || $product->discount_time_until == '' ? '' : $product->discount_time_until;

        Helper::updateProductDiscountTimeSystematic($product, $discount_time_from, $discount_time_until);

        Helper::updateProductConfirmDiscount($product);
    }

    public static function updateProductDiscountTimeSystematic($product, $discount_time_from, $discount_time_until)
    {

        $a = array();
        $a['confirm_discount'] = 0;
        $a['discount_percent'] = 0;
        $a['discount_manual'] = 0;
        $a['discount_price'] = 0;
        $a['discount_time_from'] = '';
        $a['discount_time_until'] = '';

        if ($discount_time_from != '' && $discount_time_until != '') { // when between time from and time until  
            try {
                $DateNow = now()->toJalali();

                $DTF = str_replace('/0', '/', $discount_time_from);
                $DTF = '[' . str_replace('/', ',', $DTF) . ']';
                $DTFArray = json_decode($DTF);
                $dateSpell = Verta::jalaliToGregorian($DTFArray[0], $DTFArray[1], $DTFArray[2]);
                $TimeFrom = $dateSpell != null ? $dateSpell[0] . '-' . $dateSpell[1] . '-' . $dateSpell[2] : '';


                $DTU = str_replace('/0', '/', $discount_time_until);
                $DTU = '[' . str_replace('/', ',', $DTU) . ']';
                $DTUArray = json_decode($DTU);
                $dateSpell = Verta::jalaliToGregorian($DTUArray[0], $DTUArray[1], $DTUArray[2]);
                $TimeUntil = $dateSpell != null ? $dateSpell[0] . '-' . $dateSpell[1] . '-' . $dateSpell[2] : '';

                if ($TimeUntil != '' && $TimeFrom != '') {
                    $diffHoursDateNowTimeUntil = verta($TimeUntil)->diffHours($DateNow, false);

                    $diffHoursTimeFromTimeUntil = verta($TimeUntil)->diffHours(verta($TimeFrom), false);
                    $diffHoursDateNowTimeFrom = verta($TimeFrom)->diffHours($DateNow, false);
                } else {
                    $product->update($a);
                }

                // echo 'productID  ' . $product->id . '  diffHoursTimeUntil ' . $diffHoursDateNowTimeUntil . "\n";

                if ($diffHoursDateNowTimeUntil >= 24) { //  when time now passed from time until means discount systematic is timeout and update to default 
                    // echo 'productID  ' . $product->id . " diffHoursDateNowTimeUntil  \n";
                    $product->update($a);
                } else {
                    if ($diffHoursDateNowTimeFrom <= 0 || $diffHoursTimeFromTimeUntil >= 24) { // thats means tim
                        $product->update(['discount_price' => 0]);
                        // echo 'productID  ' . $product->id . "\n";
                    } else {
                        // echo 'productID  ' . $product->id . " Ddd  \n";
                        Helper::updateProductConfirmDiscount($product);
                        if ($product->confirm_discount == 1) {
                            $discount_price = Helper::calculateDiscountPrice($product);
                            $product->update(['discount_price' => $discount_price]);
                        }
                    }
                }


            } catch (\Throwable $th) {
                $product->update($a);
            }

        } else if ($discount_time_from == '' && $discount_time_until == '') {
            // echo 'productID  ' . $product->id .'  discount_price ' . "\n";
            $b = array();
            $b['discount_time_from'] = '';
            $b['discount_time_until'] = '';
            $product->update($b);
        } else if ($discount_time_from == '' && $discount_time_until != '') {

            try {
                $DateNow = now()->toJalali();

                $DTU = str_replace('/0', '/', $discount_time_until);
                $DTU = '[' . str_replace('/', ',', $DTU) . ']';
                $DTUArray = json_decode($DTU);
                $dateSpell = Verta::jalaliToGregorian($DTUArray[0], $DTUArray[1], $DTUArray[2]);
                $TimeUntil = $dateSpell != null ? $dateSpell[0] . '-' . $dateSpell[1] . '-' . $dateSpell[2] : '';

                if ($TimeUntil != '') {
                    $diffHoursDateNowTimeUntil = verta($TimeUntil)->diffHours($DateNow, false);
                } else {
                    // echo 'productID  ' . $product->id . '  diffHoursTimeUntil  2 ' . "\n";
                    // $product->update($a);
                }

                // echo 'productID  ' . $product->id . '  diffHoursDateNowTimeUntil ' . $diffHoursDateNowTimeUntil . "\n";
                if ($diffHoursDateNowTimeUntil <= 24) { // when time is not over
                    // echo 'productID  ' . $product->id . '  diffHoursTimeUntil  <= 24  ' . "\n";
                    Helper::updateProductConfirmDiscount($product);
                    if ($product->confirm_discount == 1) {
                        $discount_price = Helper::calculateDiscountPrice($product);
                        $product->update(['discount_price' => $discount_price]);
                    }
                } else { // when time is over
                    // echo 'productID  ' . $product->id . '  diffHoursTimeUntil  >  24  ' . "\n";
                    $product->update($a);
                }
            } catch (\Throwable $th) {
                $product->update($a);
            }


        } else if ($discount_time_from != '' && $discount_time_until == '') {
            try {
                $DateNow = now()->toJalali();

                $DTF = str_replace('/0', '/', $discount_time_from);
                $DTF = '[' . str_replace('/', ',', $DTF) . ']';
                $DTFArray = json_decode($DTF);
                $dateSpell = Verta::jalaliToGregorian($DTFArray[0], $DTFArray[1], $DTFArray[2]);
                $TimeFrom = $dateSpell != null ? $dateSpell[0] . '-' . $dateSpell[1] . '-' . $dateSpell[2] : '';

                if ($TimeFrom != '') {
                    $diffHoursDateNowTimeFrom = verta($TimeFrom)->diffHours($DateNow, false);
                } else {
                    // echo 'productID  ' . $product->id . '  diffHoursTimeFrom' . "\n";
                    // $product->update($a);
                }

                // echo 'productID  ' . $product->id . '  diffHoursDateNowTimeFrom ' . $diffHoursDateNowTimeFrom . "\n";
                if ($diffHoursDateNowTimeFrom <= 0) {
                    // echo 'productID  ' . $product->id . '  diffHoursDateNowTimeFrom  <= 0 '  . "\n";
                    // $product->update($a);
                    $product->update(['discount_price' => 0]);
                } else {
                    // echo 'productID  ' . $product->id . '  diffHoursDateNowTimeFrom  >  0 '  . "\n";
                    Helper::updateProductConfirmDiscount($product);
                    if ($product->confirm_discount == 1) {
                        $discount_price = Helper::calculateDiscountPrice($product);
                        $product->update(['discount_price' => $discount_price]);
                    }
                }
            } catch (\Throwable $th) {
                $product->update($a);
            }


        }


    }
    public static function updateProductConfirmDiscount($product)
    {
        if (
            $product->discount_percent == 0 && $product->discount_manual == 0 || $product->discount_percent > 0 && $product->discount_manual > 0
            || $product->discount_percent > 100 || $product->discount_manual > $product->sale_price || $product->discount_percent < 0 
            || $product->discount_manual < 0  || $product->confirm_discount == 0
        ) {
            $a = array();
            $a['confirm_discount'] = 0;
            $a['discount_percent'] = 0;
            $a['discount_manual'] = 0;
            $a['discount_price'] = 0;
            $a['discount_time_from'] = '';
            $a['discount_time_until'] = '';

            $product->update($a);
        } else {
            if ($product->confirm_discount == 1) {
                $discount_price = Helper::calculateDiscountPrice($product);
                $product->update(['discount_price' => $discount_price]);
            }
        }
    }
    public static function calculateDiscountPrice($product)
    {
        $discountPrice = 0;

        if ($product->discount_percent == 0 && $product->discount_manual > 0) { // is discount manual

            $discountPrice = $product->discount_manual;

            return $discountPrice;
        }

        if ($product->discount_percent > 0 && $product->discount_manual == 0) { // is discount percent

            $discountPrice = $product->sale_price * ($product->discount_percent / 100);

            return $discountPrice;
        }
    }
}