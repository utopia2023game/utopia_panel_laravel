<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Http\Controllers\HistoryCustomerController;
use App\Models\Address;
use App\Models\Bank;
use App\Models\Customer;
use App\Models\Delivery;
use App\Models\HistoryCustomerOrder;
use App\Models\HistoryCustomerOrderProduct;
use App\Models\Media;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Product;
use Carbon\Carbon;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Request;
use Throwable;

class OrderController extends Controller
{
    public function sendOrderCart(Request $request)
    {
        $input = Request()->all();
        // return $input;

        $result = array();
        $result['result'] = false;
        $result['callback_value'] = '';
        $result['message'] = '';
        $result['error_product_id'] = '';

        // return verta();
        // return $input;
        $idb = $input['idb'];
        $bid = $input['bid'];
        Helper::DBConnection(env('SERVER_STATUS', '') . 'utopia_store_' . $idb);

        try {
            $checkingCustomerExists = $this->checkingCustomerExists($input['customer_id']);
            if ($checkingCustomerExists['result'] == true) {

                $checkingProductsCount = $this->checkingProductsCount($input);
                if ($checkingProductsCount['result'] == true) {

                    $checkingAddressIdExists = $this->checkingAddressIdExists($input['address_id']);
                    if ($checkingAddressIdExists['result'] == true) {
                        $checkingDeliveryType = $this->checkingDeliveryType($input);
                        if ($checkingDeliveryType['result'] == true) {
                            $checkingAllPrice = $this->checkingAllPrice($input);
                            if ($checkingAllPrice['result'] == true) {

                                if (Bank::where('id', $input['bid'])->exists()) {

                                    try {

                                        unset($input['idb']);
                                        unset($input['bid']);

                                        $input['delivery_id'] = $input['delivery_type_id'];
                                        unset($input['delivery_type_id']);

                                        $input['order_code'] = Helper::generateUniqueCode();
                                        if ($input['status'] == 'checking') {
                                            $input['status'] = 'paying';

                                            $product_id = json_decode($input['product_id']);

                                            $a = '';
                                            for ($i = 0; $i < count($product_id); $i++) {

                                                $a .= '"' . Product::where('id', $product_id[$i])->value('title') . '"';

                                                $i + 1 != count($product_id) ? $a .= ',' : null;
                                            }

                                            $input['product_name'] = '[' . $a . ']';

                                            $address = Address::where('id', $input['address_id'])->first();
                                            $input['address_receiver_name'] = $address->receiver_name;
                                            $input['address_receiver_mobile'] = $address->receiver_mobile;
                                            $input['address_receiver_address'] = $address->receiver_address;
                                            $input['address_receiver_post_code'] = $address->receiver_post_code;

                                            // return $input;
                                            $order = Order::create($input);

                                            try {
                                                Helper::clearCartByCustomerId($input['customer_id']);
                                            } catch (\Throwable $th) {}
                                        }

                                        $bank = Bank::where('id', $bid)->first() ?? '';
                                        $result['result'] = true;
                                        $result['callback_value'] = $bank->url;
                                        $result['message'] = 'checkingDataIsTrue';

                                        try {
                                            $hcDevice_id = HistoryCustomerController::setAndGetDeviceInfoId($input['device_info'], $input['customer_id']);

                                            $hcOrder = array();
                                            $hcOrder['device_history_id'] = $hcDevice_id;
                                            $hcOrder['customer_id'] = $input['customer_id'];
                                            $hcOrder['order_id'] = $order->id;
                                            $hcOrder['order_status_id'] = 1; // default 1 means pay_paying
                                            $hcOrder['execute_time'] = Carbon::now()->toDateTimeString();

                                            // return $hcOrder;

                                            HistoryCustomerOrder::create($hcOrder);
                                        } catch (\Throwable $th) {}

                                        try {
                                            if ($order != null) {
                                                $this->set_hc_order_product_table_refresh($input['customer_id'], $product_id, $order->id);
                                            }
                                        } catch (\Throwable $th) {}

                                    } catch (\Throwable $th) {
                                        $result['result'] = false;
                                        $result['message'] = 'createTableFalse' . $th;
                                    }

                                } else {
                                    $result['result'] = false;
                                    $result['message'] = 'bIdIsFalse';
                                }

                            } else {
                                $result = $checkingAllPrice;
                                return $result;
                            }
                        } else {
                            $result = $checkingDeliveryType;
                            return $result;
                        }
                    } else {
                        $result = $checkingAddressIdExists;
                        return $result;
                    }

                } else {
                    $result = $checkingProductsCount;
                    return $result;
                }
            } else {
                $result = $checkingCustomerExists;
                return $result;
            }
        } catch (Throwable $th) {
            $result['result'] = false;
            $result['message'] = 'tryCatchFalse';
        }

        return $result;
    }

    public function checkingCustomerExists($customer_id)
    {
        $result = array();
        $result['result'] = false;
        $result['callback_value'] = '';
        $result['message'] = '';
        $result['error_product_id'] = '';

        $customer_id = $customer_id == null || $customer_id == '' ? '0' : $customer_id;
        if (Customer::where('id', $customer_id)->exists()) {
            $result['result'] = true;
        } else {
            $result['result'] = false;
            $result['message'] = 'customerNotExists';
        }
        return $result;
    }
    public function checkingProductsCount($input)
    {
        $result = array();
        $result['result'] = false;
        $result['callback_value'] = '';
        $result['message'] = '';
        $result['error_product_id'] = '';

        $all_count_products = $input['all_count_products'] == null || $input['all_count_products'] == '' ? '0' : json_decode($input['all_count_products']);
        $product_id = $input['product_id'] == null || $input['product_id'] == '' ? '[]' : json_decode($input['product_id']);
        $count_selected = $input['count_selected'] == null || $input['count_selected'] == '' ? '[]' : json_decode($input['count_selected']);
        $sale_price = $input['sale_price'] == null || $input['sale_price'] == '' ? '[]' : json_decode($input['sale_price']);
        $discount_price = $input['discount_price'] == null || $input['discount_price'] == '' ? '[]' : json_decode($input['discount_price']);

        $countArray = count($product_id);
        if (count($count_selected) == $countArray && count($sale_price) == $countArray && count($discount_price) == $countArray) {
            if ($all_count_products == $countArray) {
                $product_id_not_exists = [];
                for ($i = 0; $i < count($product_id); $i++) {
                    if (!Product::where('id', $product_id[$i])->exists()) {
                        array_push($product_id_not_exists, $product_id[$i]);
                    }
                }
                if (count($product_id_not_exists) > 0) {
                    $result['result'] = false;
                    $result['message'] = 'productIdNotExists';
                    $result['error_product_id'] = $product_id_not_exists;
                } else {
                    $result['result'] = true;
                }
            } else {
                $result['result'] = false;
                $result['message'] = 'allProductsCountfalse';
            }
        } else {
            $result['result'] = false;
            $result['message'] = 'arrayCountProductsfalse';
        }

        return $result;
    }
    public function checkingAddressIdExists($address_id)
    {

        $result = array();
        $result['result'] = false;
        $result['callback_value'] = '';
        $result['message'] = '';
        $result['error_product_id'] = '';

        $address_id = $address_id == null || $address_id == '' ? '0' : $address_id;
        if (Address::where('id', $address_id)->exists()) {
            $result['result'] = true;
        } else {
            $result['result'] = false;
            $result['message'] = 'addressNotExists';
        }

        return $result;
    }
    public function checkingDeliveryType($input)
    {
        $result = array();
        $result['result'] = false;
        $result['callback_value'] = '';
        $result['message'] = '';
        $result['error_product_id'] = '';

        $delivery_type_id = $input['delivery_type_id'] == null || $input['delivery_type_id'] == '' ? '0' : $input['delivery_type_id'];
        if ($delivery_type_id == '0') {
            $result['result'] = false;
            $result['message'] = 'deliveryIdNotExists';
            return $result;
        }

        if (Delivery::where('id', $delivery_type_id)->exists()) {
            $delivery_type_name = $input['delivery_type_name'] == null || $input['delivery_type_name'] == '' ? '' : $input['delivery_type_name'];
            $delivery_type_price = $input['delivery_type_price'] == null || $input['delivery_type_price'] == '' ? '0' : $input['delivery_type_price'];
            $delivery_date = $input['delivery_date'] == null || $input['delivery_date'] == '' ? '' : $input['delivery_date'];
            $delivery_time = $input['delivery_time'] == null || $input['delivery_time'] == '' ? '' : $input['delivery_time'];

            $delivery = Delivery::where('id', $delivery_type_id)->first();

            if ($delivery->id != $delivery_type_id) {
                $result['result'] = false;
                $result['message'] = 'deliveryTypeIdNotCompare';
                return $result;
            }

            if ($delivery->name != $delivery_type_name) {
                $result['result'] = false;
                $result['message'] = 'deliveryTypeNameNotCompare';
                return $result;
            }

            if ($delivery->price != $delivery_type_price) {
                $result['result'] = false;
                $result['message'] = 'deliveryPriceNotCompare';
                return $result;
            }

            if (str_contains($input['delivery_date'], '/')) {
                if ($delivery_date != '') {
                    // verta();
                    // $DateNow = now()->toJalali();
                    $DateNow = verta()->addDays($delivery->delay_day_start);
                    $a = str_replace('/0', '/', $input['delivery_date']);
                    $a = '[' . str_replace('/', ',', $a) . ']';
                    $deliveryDateArray = json_decode($a);
                    $dateSpell = Verta::jalaliToGregorian($deliveryDateArray[0], $deliveryDateArray[1], $deliveryDateArray[2]);
                    $s = $dateSpell != null ? $dateSpell[0] . '-' . $dateSpell[1] . '-' . $dateSpell[2] : '';
                    if ($s != '') {
                        $diffDays = verta($s)->diffDays($DateNow, false);
                        if ($diffDays > 0 || $diffDays < -5) {
                            $result['result'] = false;
                            $result['message'] = 'deliveryDateIsFalse';
                            return $result;
                        }
                    } else {
                        $result['result'] = false;
                        $result['message'] = 'deliveryDateValidationIsFalse';
                        return $result;
                    }
                } else {
                    $result['result'] = false;
                    $result['message'] = 'deliveryDateIsEmpty';
                    return $result;
                }
            } else {
                $result['result'] = false;
                $result['message'] = 'deliveryDateFormatIsFalse';
                return $result;
            }
            if ($delivery_time != '') {
                $deliveryTimeArray = json_decode($input['delivery_time']);

                if ($deliveryTimeArray != null) {
                    if ($delivery->delay_time_from != $deliveryTimeArray[0] || $delivery->delay_time_until != $deliveryTimeArray[1]) {
                        $result['result'] = false;
                        $result['message'] = 'deliveryTimeIsFalse';
                        return $result;
                    }
                } else {
                    $result['result'] = false;
                    $result['message'] = 'deliveryTimeValidationIsFalse';
                    return $result;
                }

                // $result['result'] = false;
                // $result['message'] = 'deliveryTypeIdNotCompare';
                // return $result;
            }

            $result['result'] = true;
            return $result;
        } else {
            $result['result'] = false;
            $result['message'] = 'deliveryNotExists';
            return $result;
        }

    }
    public function checkingAllPrice($input)
    {
        $result = array();
        $result['result'] = true;
        $result['callback_value'] = '';
        $result['message'] = '';
        $result['error_product_id'] = '';

        $all_sale_price_products = 0;
        $all_discount_price_products = 0;
        $all_price_all = 0;

        $product_id = json_decode($input['product_id']);
        $count_selected = json_decode($input['count_selected']);
        $sale_price = json_decode($input['sale_price']);
        $discount_price = json_decode($input['discount_price']);

        $product_id_count_selected_not_exists = [];
        $product_id_sale_price_not_exists = [];
        $product_message_discount_price_not_exists = [];
        $product_id_discount_price_not_exists = [];

        for ($i = 0; $i < count($product_id); $i++) {

            $product = Product::where('id', $product_id[$i])->first();

            if (!$this->checkingProductsCountSelected($product, $count_selected[$i])) {
                array_push($product_id_count_selected_not_exists, $product_id[$i]);
                // echo 'product_id  ' . $product_id[$i] . '   ' . count($product_id_count_selected_not_exists) . "\n";
            }

            if (!$this->checkingProductsSalePrice($product, $sale_price[$i])) {
                array_push($product_id_sale_price_not_exists, $product_id[$i]);
            }

            $checkingProductsDiscountPrice = $this->checkingProductsDiscountPrice($product, $discount_price[$i]);
            if (!$checkingProductsDiscountPrice['result']) {
                array_push($product_id_discount_price_not_exists, $product_id[$i]);
                // echo $checkingProductsDiscountPrice['message'];
                array_push($product_message_discount_price_not_exists, $checkingProductsDiscountPrice['message'] == '' ? 'productIdDiscountPriceNotExists' : $checkingProductsDiscountPrice['message']);
            }

            $all_sale_price_products += $count_selected[$i] * $sale_price[$i];
            $all_discount_price_products += $count_selected[$i] * $discount_price[$i];

        }

        $all_price_all = $all_sale_price_products - $all_discount_price_products + intval($input['delivery_type_price']);

        // echo count($product_id_count_selected_not_exists);

        if (count($product_id_count_selected_not_exists) > 0) {
            $result['result'] = false;
            $result['message'] = 'productIdCountSelectedNotExists';
            $result['error_product_id'] = json_encode($product_id_count_selected_not_exists);
            return $result;
        }

        if (count($product_id_sale_price_not_exists) > 0) {
            $result['result'] = false;
            $result['message'] = 'productIdSalePriceNotExists';
            $result['error_product_id'] = json_encode($product_id_sale_price_not_exists);
            return $result;
        }

        if (count($product_id_discount_price_not_exists) > 0) {
            $result['result'] = false;
            // $result['message'] = 'productIdDiscountPriceNotExists';
            $result['message'] = $product_message_discount_price_not_exists;
            $result['error_product_id'] = json_encode($product_id_discount_price_not_exists);
            return $result;
        }

        if ($all_sale_price_products != $input['all_sale_price_products']) {
            $result['result'] = false;
            $result['message'] = 'AllSalePriceNotCompare';
            return $result;
        }

        if ($all_discount_price_products != $input['all_discount_price_products']) {
            $result['result'] = false;
            $result['message'] = 'AllDiscountPriceNotCompare';
            return $result;
        }

        // echo 'all_price_all ' . $all_price_all . '   input ' . $input['all_price_all'] . "\n";
        if ($all_price_all != $input['all_price_all']) {
            $result['result'] = false;
            $result['message'] = 'AllPriceAllNotCompare';
            return $result;
        }

        return $result;
    }

    public function checkingProductsCountSelected($product, $count_selected)
    {
        $stack_status = $product->stack_status;
        $stack_count = $product->stack_count == null || $product->stack_count == '' ? 0 : $product->stack_count;
        $stack_limit = $product->stack_limit == null || $product->stack_limit == '' ? $stack_count : $product->stack_limit;

        $stack_count == 0 ? $stack_limit = 0 : null;

        if ($stack_status != 0) {
            // echo 'product_id ' . $product->id . '  stack_status ' . $stack_status . "\n";
            return false;
        } else {
            if ($count_selected > $stack_count) {
                // echo 'product_id ' . $product->id . ' count_selected ' . $count_selected . '  stack_count ' . $stack_count . "\n";
                return false;
            } else {
                if ($count_selected > $stack_limit) {
                    // echo 'product_id ' . $product->id . ' count_selected ' . $count_selected . '  stack_limit ' . $stack_limit . "\n";
                    return false;
                }
            }
        }
        return true;
    }

    public function checkingProductsSalePrice($product, $salePrice)
    {

        $sale_price = $product->sale_price == null || $product->sale_price == '' ? 0 : $product->sale_price;

        if ($sale_price != $salePrice) {
            return false;
        }
        return true;
    }

    public function checkingProductsDiscountPrice($product, $discounPrice)
    {
        $result = array();
        $result['result'] = true;
        $result['message'] = '';

        $discount_time_from = $product->discount_time_from == null || $product->discount_time_from == '' ? '' : $product->discount_time_from;
        $discount_time_until = $product->discount_time_until == null || $product->discount_time_until == '' ? '' : $product->discount_time_until;

        $updateProductDiscountTimeSystematic = $this->updateProductDiscountTimeSystematic($product, $discount_time_from, $discount_time_until);
        // $updateProductDiscountTimeSystematic = Helper::updateProductDiscountTimeSystematic($product, $discount_time_from, $discount_time_until);
        if ($updateProductDiscountTimeSystematic['result']) {
            // $discount_percent = $product->discount_percent == null || $product->discount_percent == '' ? 0 : $product->discount_percent;
            // $discount_manual = $product->discount_manual == null || $product->discount_manual == '' ? 0 : $product->discount_manual;
            // $discount_price = $product->discount_price == null || $product->discount_price == '' ? 0 : $product->discount_price;

            // $this->updateProductConfirmDiscount($product, $discount_percent, $discount_manual, $discount_price);
            Helper::updateProductConfirmDiscount($product);

            $confirm_discount = $product->confirm_discount == null || $product->confirm_discount == '' ? 0 : $product->confirm_discount;

            if ($confirm_discount == 1) {
                if ($discounPrice == 0) {
                    $result['result'] = false;
                    $result['message'] = 'discountPriceZeroConfirmDiscountOne';
                }
            } else {
                if ($discounPrice > 0) {
                    $result['result'] = false;
                    $result['message'] = 'discountPricePlusZeroConfirmDiscountZero';
                }
            }
        } else {
            $result['result'] = false;
            $result['message'] = $updateProductDiscountTimeSystematic['message'];
        }

        return $result;
    }

    public function updateProductDiscountTimeSystematic($product, $discount_time_from, $discount_time_until)
    {
        $result = array();
        $result['result'] = true;
        $result['message'] = '';

        $a = array();
        $a['confirm_discount'] = 0;
        $a['discount_percent'] = 0;
        $a['discount_manual'] = 0;
        $a['discount_price'] = 0;
        $a['discount_time_from'] = '';
        $a['discount_time_until'] = '';

        if ($discount_time_from != '' && !str_contains($discount_time_from, '/') || $discount_time_until != '' && !str_contains($discount_time_until, '/')) {
            $product->update($a);
            // echo 'productID  ' . $product->id . '  str_contains ' . "\n";
        } else {
            if ($discount_time_from != '' && $discount_time_until != '') {

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
                    $diffHoursTimeUntil = verta($TimeUntil)->diffHours(verta($TimeFrom), false);
                } else {

                    // echo 'productID  ' . $product->id . '  diffHoursTimeUntil ' . "\n";
                    $product->update($a);
                }

                $diffHoursTimeFrom = verta($TimeFrom)->diffHours($DateNow, false);

                // echo 'productID  ' . $product->id . '  diffHoursTimeFrom ' . $diffHoursTimeFrom . '  diffHoursTimeUntil ' . $diffHoursTimeUntil . "\n";
                if ($diffHoursTimeFrom <= 0 || $diffHoursTimeUntil >= 0) {
                    // $product->update($a);
                    // echo 'productID  ' . $product->id . '  diffHoursTimeFrom ' . $diffHoursTimeFrom . "\n";
                    $result['result'] = false;
                    $result['message'] = 'diffHoursTimeFromUntilFalse';
                    return $result;
                }
            } else if ($discount_time_from == '' && $discount_time_until == '') {
                // echo 'productID  ' . $product->id .'  discount_price ' . "\n";
                $b = array();
                // $b['discount_price'] = 0;
                $b['discount_time_from'] = '';
                $b['discount_time_until'] = '';
                $product->update($b);
            } else if ($discount_time_from == '' && $discount_time_until != '') {
                $DateNow = now()->toJalali();

                $DTU = str_replace('/0', '/', $discount_time_until);
                $DTU = '[' . str_replace('/', ',', $DTU) . ']';
                $DTUArray = json_decode($DTU);
                $dateSpell = Verta::jalaliToGregorian($DTUArray[0], $DTUArray[1], $DTUArray[2]);
                $TimeUntil = $dateSpell != null ? $dateSpell[0] . '-' . $dateSpell[1] . '-' . $dateSpell[2] : '';

                if ($TimeUntil != '') {
                    $diffHoursTimeUntil = verta($TimeUntil)->diffHours($DateNow, false);
                } else {
                    // echo 'productID  ' . $product->id . '  diffHoursTimeUntil  2 ' . "\n";
                    $product->update($a);
                }

                // echo 'productID  ' . $product->id . '  diffHoursTimeUntil  < 0  ' . $diffHoursTimeUntil  . "\n";
                if ($diffHoursTimeUntil >= 0) {
                    // echo 'productID  ' . $product->id . '  diffHoursTimeUntil  < 0  ' . "\n";
                    // $product->update($a);
                    $result['result'] = false;
                    $result['message'] = 'TimeUntilFalse';
                    return $result;
                }

            } else if ($discount_time_from != '' && $discount_time_until == '') {
                $DateNow = now()->toJalali();

                $DTF = str_replace('/0', '/', $discount_time_from);
                $DTF = '[' . str_replace('/', ',', $DTF) . ']';
                $DTFArray = json_decode($DTF);
                $dateSpell = Verta::jalaliToGregorian($DTFArray[0], $DTFArray[1], $DTFArray[2]);
                $TimeFrom = $dateSpell != null ? $dateSpell[0] . '-' . $dateSpell[1] . '-' . $dateSpell[2] : '';

                if ($TimeFrom != '') {
                    $diffHoursTimeFrom = verta($TimeFrom)->diffHours($DateNow, false);
                } else {
                    // echo 'productID  ' . $product->id . '  diffHoursTimeFrom' . "\n";
                    $product->update($a);
                }

                if ($diffHoursTimeFrom <= 0) {
                    // echo 'productID  ' . $product->id . '  diffHoursTimeFrom  < 0 ' . $diffHoursTimeFrom . "\n";
                    $result['result'] = false;
                    $result['message'] = 'TimeFromFalse';
                    return $result;
                    // $product->update($a);
                }

            }
        }

        return $result;
    }
    
    
    // public function updateProductConfirmDiscount($product, $discount_percent, $discount_manual, $discount_price)
    // {
    //     if ($discount_percent == 0 && $discount_manual == 0 || $discount_percent > 0 && $discount_manual > 0 || $discount_price == 0 && $discount_percent > 0 || $discount_price == 0 && $discount_manual > 0) {
    //         $a = array();
    //         $a['confirm_discount'] = 0;
    //         $a['discount_percent'] = 0;
    //         $a['discount_manual'] = 0;
    //         $a['discount_price'] = 0;
    //         $a['discount_time_from'] = '';
    //         $a['discount_time_until'] = '';

    //         $product->update($a);
    //     }
    // }

    public function set_hc_order_product_table_refresh($customer_id, $product_id, $order_id)
    {
        $order = Order::find($order_id);
        // dd($customer_id , $product_id , $order->order_status_id,$order);
        HistoryCustomerOrderProductController::set_data_history_customer_order_product($customer_id, $product_id, $order);
        $hc_status['hc_order_product_status'] = 1;
        Order::where('id', $order_id)->update($hc_status);
    }

    public function listOrders()
    {
        $input = Request()->all();

        Helper::DBConnection(env('SERVER_STATUS', '') . 'utopia_store_' . $input['idb']);

        $order_status = OrderStatus::where('name', 'LIKE', '%' . $input['status'] . '%')->get('id');

        $order_status_id = array();
        for ($f = 0; $f < count($order_status); $f++) {
            array_push($order_status_id, $order_status[$f]->id);
        }

        $Order = Order::where('customer_id', $input['customer_id'])->whereIn('order_status_id', $order_status_id)->orderBy('id' , 'desc')->get();

        for ($i = 0; $i < count($Order); $i++) {
            $ids = json_decode($Order[$i]['product_id']);
            $b['product_image'] = array();
            // $d = '';
            for ($j = 0; $j < count($ids); $j++) {
                // echo $ids[$j] . "\n";
                $a = '';
                if (Media::where('product_id', $ids[$j])->where('type', 'image')->exists()) {
                    $res = Media::where('product_id', $ids[$j])->where('type', 'image')->where('priority', 1)->first();
                    $a = $res->path ?? '';
                }

                if ($a == '' && Media::where('product_id', $ids[$j])->where('type', 'image')->exists()) {
                    $res = Media::where('product_id', $ids[$j])->where('type', 'image')->first();
                    $a = $res->path ?? '';
                }
                array_push($b['product_image'], $a);

                // $d .= '"' . Product::where('id', $ids[$j])->value('title') . '"';

                // $j + 1 == count($ids) ? null : $d .= ',';
            }
            $Order[$i]['product_image'] = $b['product_image'];

            // $c['product_name'] = '[' . $d . ']';
            // Order::where('id', $Order[$i]['id'])->update($c);
        }

        return $Order;
    }

    public function listOrdersManagement()
    {
        $input = Request()->all();

        Helper::DBConnection(env('SERVER_STATUS', '') . 'utopia_store_' . $input['idb']);

        if ($input['status'] == 0) {
            $Order = Order::all();
        } else {
            $Order = Order::where('order_status_id', $input['status'])->get();
        }

        for ($i = 0; $i < count($Order); $i++) {
            $ids = json_decode($Order[$i]['product_id']);
            $b['product_image'] = array();
            // $d = '';
            for ($j = 0; $j < count($ids); $j++) {
                // echo $ids[$j] . "\n";
                $a = '';
                if (Media::where('product_id', $ids[$j])->where('type', 'image')->exists()) {
                    $res = Media::where('product_id', $ids[$j])->where('type', 'image')->where('priority', 1)->first();
                    $a = $res->path ?? '';
                }

                if ($a == '' && Media::where('product_id', $ids[$j])->where('type', 'image')->exists()) {
                    $res = Media::where('product_id', $ids[$j])->where('type', 'image')->first();
                    $a = $res->path ?? '';
                }
                array_push($b['product_image'], $a);
            }
            $Order[$i]['product_image'] = $b['product_image'];
        }

        return $Order;
    }

    public function changeStatus(Request $request)
    {
        $input = $request->all();
        $result = 0;
        $id = $input['id'];
        $order_status_id = $input['order_status_id'];
        $customer_id = $input['customer_id'];
        $device_info = $input['device_info'];

        Helper::DBConnection(env('SERVER_STATUS', '') . 'utopia_store_' . $input['idb']);

        if (!OrderStatus::find($order_status_id)->exists()) {
            return $result;
        }

        if (Order::where('id', $id)->where('customer_id', $customer_id)->exists()) {
            $order = Order::where('id', $id)->where('customer_id', $customer_id)->first();
            $order_status_id_old = $order->order_status_id;
            if ($order_status_id_old == $order_status_id) {
                return $result;
            }
            // dd($result);
            try {
                $result = $order->update([
                    'order_status_id' => $order_status_id,
                ]);
            } catch (\Throwable $th) {
                dd($th);
            }

            if ($result) {
                try {
                    $hcDevice_id = HistoryCustomerController::setAndGetDeviceInfoId($input['device_info'], $input['customer_id']);

                    $hcOrder = array();
                    $hcOrder['device_history_id'] = $hcDevice_id;
                    $hcOrder['customer_id'] = $input['customer_id'];
                    $hcOrder['order_id'] = $order->id;
                    $hcOrder['order_status_id'] = $order_status_id; // default 1 means pay_paying
                    $hcOrder['execute_time'] = Carbon::now()->toDateTimeString();

                    // return $hcOrder;

                    HistoryCustomerOrder::create($hcOrder);
                } catch (\Throwable $th) {}

                try {
                    $order = Order::where('customer_id', $customer_id)->get();
                    // echo 'customer_id '. $customer_id .' order ' .($order) ."\n";
                    HistoryCustomerOrderProduct::where('customer_id', $customer_id)->forcedelete();
                    for ($j = 0; $j < count($order); $j++) {
                        $product_id = json_decode($order[$j]->product_id);
                        HistoryCustomerOrderProductController::set_data_history_customer_order_product($customer_id, $product_id, $order[$j]);
                    }

                } catch (\Throwable $th) {}
            }
        }

        return $result;
    }

}
