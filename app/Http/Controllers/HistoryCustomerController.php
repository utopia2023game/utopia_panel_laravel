<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Models\HistoryCustomerCart;
use App\Models\HistoryCustomerLike;
use App\Models\HistoryCustomerUser;
use App\Models\HistoryCustomerView;
use App\Models\HistoryCustomerShare;
use App\Models\HistoryCustomerDevice;
use App\Models\HistoryCustomerCategory;
use App\Models\HistoryCustomerNextCart;

class HistoryCustomerController extends Controller
{

    public static function setAndGetDeviceInfoId($input_device_info, $input_customer_id)
    {
        $deviceInfo = array();
        $deviceInfo = $input_device_info;
        $deviceInfo['customer_id'] = $input_customer_id;

        try {
            $a = HistoryCustomerDevice::where('unique_id', $deviceInfo['unique_id'])->exists();

            // return $a;
            if ($a) {
                $hcDevice = HistoryCustomerDevice::where('unique_id', $deviceInfo['unique_id'])->first();
                // return $hcDevice->id . " ddd";
            }else{
                $hcDevice = HistoryCustomerDevice::create($deviceInfo);
                // return $hcDevice . " ccc";
            }

        } catch (\Throwable $th) {
            $deviceInfo['unique_id'] = Helper::generateUnique();
            $hcDevice = HistoryCustomerDevice::create($deviceInfo);
            // return $hcDevice . " fff " . $th;
        }

        if ($hcDevice->id == null) {
            $hcDevice = HistoryCustomerDevice::where('customer_id', $input_customer_id)->latest();
            // return $hcDevice . " aaa";
        }

        return $hcDevice->id;
    }
    public function setHcCategory(Request $request)
    {
        $input = $request->all();

        // return ($input);

        Helper::DBConnection(env('SERVER_STATUS', '') . 'utopia_store_' . $input['idb']);

        try {
            if ($input['customer_id'] != 'guest' && $input['device_info'] != '') {

                $hcDevice_id = $this->setAndGetDeviceInfoId($input['device_info'], $input['customer_id']);

                if ($hcDevice_id != null || $hcDevice_id != 0) {
                    $hcCategory = array();
                    $hcCategory = $input['hc_category_list'];
                    try {
                        for ($i = 0; $i < count($hcCategory); $i++) {
                            $hcCategory[$i]['device_history_id'] = $hcDevice_id;
                            $hcCategory[$i]['customer_id'] == 0 ? $hcCategory[$i]['customer_id'] = $input['customer_id'] : null;

                            // return gettype($hcCategory[$i]);
                            HistoryCustomerCategory::create($hcCategory[$i]);
                        }
                    } catch (\Throwable $th) {
                        return 0;
                    }
                } else {
                    return 0;
                }




                return 1;
            }
        } catch (\Throwable $th) {
            return 0;
        }

        // return $input;
        return 0;
    }

    public function setHcView(Request $request)
    {
        $input = $request->all();

        // return ($input);

        Helper::DBConnection(env('SERVER_STATUS', '') . 'utopia_store_' . $input['idb']);

        try {
            if ($input['customer_id'] != 'guest' && $input['device_info'] != '') {

                $hcDevice_id = $this->setAndGetDeviceInfoId($input['device_info'], $input['customer_id']);

                if ($hcDevice_id != null || $hcDevice_id != 0) {
                    $hcView = array();
                    $hcView = $input['hc_view_list'];
                    try {
                        for ($i = 0; $i < count($hcView); $i++) {
                            $hcView[$i]['device_history_id'] = $hcDevice_id;
                            $hcView[$i]['customer_id'] == 0 ? $hcView[$i]['customer_id'] = $input['customer_id'] : null;
                            HistoryCustomerView::create($hcView[$i]);
                        }
                    } catch (\Throwable $th) {
                        return 0;
                    }
                } else {
                    return 0;
                }

                return 1;
            }
        } catch (\Throwable $th) {
            return 0;
        }

        // return $input;
        return 0;
    }
    public function setHcCart(Request $request)
    {
        $input = $request->all();

        // return ($input);

        Helper::DBConnection(env('SERVER_STATUS', '') . 'utopia_store_' . $input['idb']);

        try {
            if ($input['customer_id'] != 'guest' && $input['device_info'] != '') {
                $hcDevice_id = $this->setAndGetDeviceInfoId($input['device_info'], $input['customer_id']);

                if ($hcDevice_id != null || $hcDevice_id != 0) {
                    $hcCart = array();
                    $hcCart = $input['hc_cart_list'];
                    try {
                        for ($i = 0; $i < count($hcCart); $i++) {
                            $hcCart[$i]['device_history_id'] = $hcDevice_id;
                            $hcCart[$i]['customer_id'] == 0 ? $hcCart[$i]['customer_id'] = $input['customer_id'] : null;
                            HistoryCustomerCart::create($hcCart[$i]);
                        }
                    } catch (\Throwable $th) {
                        return 0;
                    }
                } else {
                    return 0;
                }

                return 1;
            }
        } catch (\Throwable $th) {
            return 0;
        }

        // return $input;
        return 0;
    }

    public function setHcNextCart(Request $request)
    {
        $input = $request->all();

        // return ($input);

        Helper::DBConnection(env('SERVER_STATUS', '') . 'utopia_store_' . $input['idb']);

        try {
            if ($input['customer_id'] != 'guest' && $input['device_info'] != '') {
                $hcDevice_id = $this->setAndGetDeviceInfoId($input['device_info'], $input['customer_id']);
                $hcNextCart = array();
                $hcNextCart = $input['hc_next_cart_list'];
                try {
                    for ($i = 0; $i < count($hcNextCart); $i++) {
                        $hcNextCart[$i]['device_history_id'] = $hcDevice_id;
                        $hcNextCart[$i]['customer_id'] == 0 ? $hcNextCart[$i]['customer_id'] = $input['customer_id'] : null;
                        HistoryCustomerNextCart::create($hcNextCart[$i]);
                    }
                } catch (\Throwable $th) {
                    return 0;
                }
                if ($hcDevice_id != null || $hcDevice_id != 0) {

                } else {
                    return 0;
                }

                return 1;
            }
        } catch (\Throwable $th) {
            return 0;
        }

        // return $input;
        return 0;
    }

    public function setHcLike(Request $request)
    {
        $input = $request->all();

        // return ($input);

        Helper::DBConnection(env('SERVER_STATUS', '') . 'utopia_store_' . $input['idb']);

        try {
            if ($input['customer_id'] != 'guest' && $input['device_info'] != '') {
                $hcDevice_id = $this->setAndGetDeviceInfoId($input['device_info'], $input['customer_id']);

                if ($hcDevice_id != null || $hcDevice_id != 0) {
                    $hcLike = array();
                    $hcLike = $input['hc_like_list'];
                    try {
                        for ($i = 0; $i < count($hcLike); $i++) {
                            $hcLike[$i]['device_history_id'] = $hcDevice_id;
                            $hcLike[$i]['customer_id'] == 0 ? $hcLike[$i]['customer_id'] = $input['customer_id'] : null;
                            HistoryCustomerLike::create($hcLike[$i]);
                        }
                    } catch (\Throwable $th) {
                        return 0;
                    }
                } else {
                    return 0;
                }

                return 1;
            }
        } catch (\Throwable $th) {
            return 0;
        }

        // return $input;
        return 0;
    }

    public function setHcShare(Request $request)
    {
        $input = $request->all();

        // return ($input);

        Helper::DBConnection(env('SERVER_STATUS', '') . 'utopia_store_' . $input['idb']);

        try {
            if ($input['customer_id'] != 'guest' && $input['device_info'] != '') {

                $hcDevice_id = $this->setAndGetDeviceInfoId($input['device_info'], $input['customer_id']);

                // return $hcDevice_id;

                if ($hcDevice_id != null || $hcDevice_id != 0) {
                    $hcShare = array();
                    $hcShare = $input['hc_share_list'];
                    try {
                        for ($i = 0; $i < count($hcShare); $i++) {
                            $hcShare[$i]['device_history_id'] = $hcDevice_id;
                            $hcShare[$i]['customer_id'] == 0 ? $hcShare[$i]['customer_id'] = $input['customer_id'] : null;
                            
                            HistoryCustomerShare::create($hcShare[$i]);
                        }
                    } catch (\Throwable $th) {
                        return 3;
                    }
                } else {
                    return 2;
                }



                return 1;
            }
        } catch (\Throwable $th) {
            return 4;
        }

        // return $input;
        return 0;
    }
    public function setHcUser(Request $request)
    {
        $input = $request->all();

        // return ($input);

        Helper::DBConnection(env('SERVER_STATUS', '') . 'utopia_store_' . $input['idb']);

        try {
            if ($input['customer_id'] != 'guest' && $input['device_info'] != '') {

                $hcDevice_id = $this->setAndGetDeviceInfoId($input['device_info'], $input['customer_id']);

                if ($hcDevice_id != null || $hcDevice_id != 0) {
                    $hcUser = array();
                    $hcUser = $input['hc_user_list'];
                    try {
                        for ($i = 0; $i < count($hcUser); $i++) {
                            $hcUser[$i]['device_history_id'] = $hcDevice_id;
                            $hcUser[$i]['customer_id'] == 0 ? $hcUser[$i]['customer_id'] = $input['customer_id'] : null;
                            HistoryCustomerUser::create($hcUser[$i]);
                        }
                    } catch (\Throwable $th) {
                        return 0;
                    }
                } else {
                    return 0;
                }

                return 1;
            }
        } catch (\Throwable $th) {
            return 0;
        }

        // return $input;
        return 0;
    }
}
