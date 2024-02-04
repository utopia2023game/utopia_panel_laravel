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
                                                $this->set_hc_order_product_table_refresh($input['customer_id'], $product_id, $order);
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
        if ($updateProductDiscountTimeSystematic['result']) {
            $discount_percent = $product->discount_percent == null || $product->discount_percent == '' ? 0 : $product->discount_percent;
            $discount_manual = $product->discount_manual == null || $product->discount_manual == '' ? 0 : $product->discount_manual;
            $discount_price = $product->discount_price == null || $product->discount_price == '' ? 0 : $product->discount_price;

            $this->updateProductConfirmDiscount($product, $discount_percent, $discount_manual, $discount_price);

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
                $b['discount_price'] = 0;
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
    public function updateProductConfirmDiscount($product, $discount_percent, $discount_manual, $discount_price)
    {
        if ($discount_percent == 0 && $discount_manual == 0 || $discount_percent > 0 && $discount_manual > 0 || $discount_price == 0 && $discount_percent > 0 || $discount_price == 0 && $discount_manual > 0) {
            $a = array();
            $a['confirm_discount'] = 0;
            $a['discount_percent'] = 0;
            $a['discount_manual'] = 0;
            $a['discount_price'] = 0;
            $a['discount_time_from'] = '';
            $a['discount_time_until'] = '';

            $product->update($a);
        }
    }

    public function set_hc_order_product_table_refresh($customer_id, $product_id, $order)
    {
        for ($i = 0; $i < count($product_id); $i++) {
            echo ' product_id [$i] ' . ($product_id[$i]) . "\n";

            $order_status_id = $order->order_status_id;
            $key = array_search($product_id[$i], $product_id);

            $count_selected_array = json_decode($order->count_selected);
            $count_selected = $count_selected_array[$key] ?? 1;
            $sale_price_array = json_decode($order->sale_price);
            $sale_price = $sale_price_array[$key] ?? 0;
            $discount_price_array = json_decode($order->discount_price);
            $discount_price = $discount_price_array[$key] ?? 0;
            $order_discount_times = $discount_price > 0 ? 1 : 0;
            try {
                $delivery_price = intval($order->delivery_type_price);
            } catch (\Throwable $th) {
                $delivery_price = -1;
            }
            $order_discount_times = $discount_price > 0 ? 1 : 0;
            try {
                $all_count_products = intval($order->all_count_products);
            } catch (\Throwable $th) {
                $all_count_products = count($count_selected_array);
            }
            $all_order_free_delivery_times = $delivery_price == 0 ? 1 : 0;

            if (HistoryCustomerOrderProduct::where('customer_id', $customer[$i]->id)->where('product_id', $product_id[$i])->exists()) {
                $orderProduct = HistoryCustomerOrderProduct::where('customer_id', $customer[$i]->id)->where('product_id', $product_id[$i])->first();

                $a = array();
                $a['customer_id'] = $customer[$i]->id;
                $a['product_id'] = $product_id[$i];

                $a['all_order_times'] = intval($orderProduct->all_order_times) + 1;
                $a['all_order_discount_times'] = intval($orderProduct->all_order_discount_times) + $order_discount_times;
                $a['all_order_free_delivery_times'] = intval($orderProduct->all_order_free_delivery_times) + $all_order_free_delivery_times;
                $a['all_product_count'] = intval($orderProduct->all_product_count) + $count_selected;
                $a['all_product_discount_count'] = intval($orderProduct->all_product_discount_count) + ($order_discount_times * $count_selected);
                $a['all_product_free_delivery_count'] = intval($orderProduct->all_product_free_delivery_count) + ($all_order_free_delivery_times * $count_selected);
                $a['all_total_product_pay_price'] = intval($orderProduct->all_total_product_pay_price) + round($sale_price * $count_selected);
                $a['all_total_product_discount'] = intval($orderProduct->all_total_product_discount) + round($order_discount_times * $discount_price * $count_selected);
                $a['all_avg_product_pay_price'] = round($a['all_total_product_pay_price'] / $a['all_product_count']);
                $a['all_avg_product_discount'] = $a['all_product_discount_count'] > 0 ? round($a['all_total_product_discount'] / $a['all_product_discount_count']) : 0;
                $a['all_total_order_delivery'] = intval($orderProduct->all_total_order_delivery) + round($delivery_price);
                $a['all_total_product_delivery'] = intval($orderProduct->all_total_product_delivery) + round($delivery_price / $all_count_products);
                $a['all_avg_product_delivery'] = ($a['all_product_count'] - $a['all_product_free_delivery_count']) > 0 ? round($a['all_total_product_delivery'] / ($a['all_product_count'] - $a['all_product_free_delivery_count'])) : 0;
                $a['all_order_last_date'] = verta($order->created_at);

                $fristDate = $orderProduct->all_order_first_date;
                $lastDatelast = $orderProduct->all_order_last_date;
                $lastDateNow = strval(verta($order->created_at));
                $lastDate = SUBSTR($lastDateNow, 0, 10);
                $diffDays = verta($lastDatelast)->diffDays($lastDateNow, false);
                $diffDaysWithFirst = verta($lastDatelast)->diffDays($fristDate, false);

                // dd($fristDate, $lastDatelast ,$lastDateNow , $lastDate, $diffDays ,$diffDaysWithFirst);
                $all_purchase_date = array();
                // dd(json_decode($orderProduct->all_purchase_date) ,json_decode('["1402-11-09"]'), $orderProduct->all_purchase_date);
                $orderProduct->all_purchase_date != null ? $all_purchase_date = json_decode($orderProduct->all_purchase_date) : null;
                array_push($all_purchase_date, $lastDate);

                $all_purchase_day = array();
                $orderProduct->all_purchase_date != null ? $all_purchase_day = json_decode($orderProduct->all_purchase_day) : null;
                array_push($all_purchase_day, abs($diffDaysWithFirst));

                $all_purchase_sequence_day = array();
                $orderProduct->all_purchase_date != null ? $all_purchase_sequence_day = json_decode($orderProduct->all_purchase_sequence_day) : null;
                // if ($customer[$i]->id == 2 && $product_id[$i]) {
                //     echo $diffDays . "\n";
                // }
                array_push($all_purchase_sequence_day, abs($diffDays));

                // $all_purchase_sequence_day = array_filter($all_purchase_sequence_day);
                // dd(array_sum($all_purchase_sequence_day), count($all_purchase_sequence_day));
                $all_purchase_avg_sequence_day = count($all_purchase_sequence_day) == 0 ? 0 : array_sum($all_purchase_sequence_day) / count($all_purchase_sequence_day);

                $all_purchase_pressture_day = $this->get_pressture_day($all_purchase_sequence_day);
                $all_purchase_power_in_month = $this->get_power_in_month($all_purchase_date);
                $all_purchase_power_in_year = $this->get_power_in_year($all_purchase_date);

                // $k == 2 ? dd($k, $all_purchase_date, $all_purchase_day, $all_purchase_sequence_day, $all_purchase_avg_sequence_day, $all_purchase_pressture_day, $all_purchase_power_in_month, $all_purchase_pressture_day) : null;

                $a['all_purchase_date'] = $all_purchase_date;
                $a['all_purchase_day'] = $all_purchase_day;
                $a['all_purchase_total_day'] = abs($diffDaysWithFirst);
                $a['all_purchase_sequence_day'] = $all_purchase_sequence_day;
                $a['all_purchase_avg_sequence_day'] = round($all_purchase_avg_sequence_day);
                $a['all_purchase_pressture_day'] = $all_purchase_pressture_day;
                $a['all_purchase_power_in_month'] = $all_purchase_power_in_month;
                $a['all_purchase_power_in_year'] = $all_purchase_power_in_year;

                if ($order_status_id <= 5) {
                    // $order_status = 'pending';

                    $a['pending_order_times'] = intval($orderProduct->pending_order_times) + 1;
                    $a['pending_order_discount_times'] = intval($orderProduct->pending_order_discount_times) + $order_discount_times;
                    $a['pending_order_free_delivery_times'] = intval($orderProduct->pending_order_free_delivery_times) + $all_order_free_delivery_times;
                    $a['pending_product_count'] = intval($orderProduct->pending_product_count) + $count_selected;
                    $a['pending_product_discount_count'] = intval($orderProduct->pending_product_discount_count) + ($order_discount_times * $count_selected);
                    $a['pending_product_free_delivery_count'] = intval($orderProduct->pending_product_free_delivery_count) + ($all_order_free_delivery_times * $count_selected);
                    $a['pending_total_product_pay_price'] = intval($orderProduct->pending_total_product_pay_price) + round($sale_price * $count_selected);
                    $a['pending_total_product_discount'] = intval($orderProduct->pending_total_product_discount) + round($order_discount_times * $discount_price * $count_selected);
                    $a['pending_avg_product_pay_price'] = round($a['pending_total_product_pay_price'] / $a['pending_product_count']);
                    $a['pending_avg_product_discount'] = $a['pending_product_discount_count'] > 0 ? round($a['pending_total_product_discount'] / $a['pending_product_discount_count']) : 0;
                    $a['pending_total_order_delivery'] = intval($orderProduct->pending_total_order_delivery) + round($delivery_price);
                    $a['pending_total_product_delivery'] = intval($orderProduct->pending_total_product_delivery) + round($delivery_price / $all_count_products);
                    $a['pending_avg_product_delivery'] = ($a['pending_product_count'] - $a['pending_product_free_delivery_count']) > 0 ? round($a['pending_total_product_delivery'] / ($a['pending_product_count'] - $a['pending_product_free_delivery_count'])) : 0;
                    $a['pending_order_last_date'] = verta($order->created_at);

                    $fristDate = verta($orderProduct->pending_order_first_date);
                    $lastDatelast = verta($orderProduct->pending_order_last_date);
                    $lastDateNow = strval(verta($order->created_at));
                    $lastDate = SUBSTR($lastDateNow, 0, 10);
                    $diffDays = verta($lastDatelast)->diffDays($lastDateNow, false);
                    $diffDaysWithFirst = verta($lastDatelast)->diffDays($fristDate, false);

                    $pending_purchase_date = array();
                    $orderProduct->pending_purchase_date != null ? $pending_purchase_date = json_decode($orderProduct->pending_purchase_date) : null;
                    array_push($pending_purchase_date, $lastDate);

                    $pending_purchase_day = array();
                    $orderProduct->pending_purchase_date != null ? $pending_purchase_day = json_decode($orderProduct->pending_purchase_day) : null;
                    array_push($pending_purchase_day, abs($diffDaysWithFirst));

                    $pending_purchase_sequence_day = array();
                    $orderProduct->pending_purchase_date != null ? $pending_purchase_sequence_day = json_decode($orderProduct->pending_purchase_sequence_day) : null;
                    array_push($pending_purchase_sequence_day, abs($diffDays));

                    $pending_purchase_sequence_day = array_filter($pending_purchase_sequence_day);
                    $pending_purchase_avg_sequence_day = count($pending_purchase_sequence_day) == 0 ? 0 : array_sum($pending_purchase_sequence_day) / count($pending_purchase_sequence_day);

                    $pending_purchase_pressture_day = $this->get_pressture_day($pending_purchase_sequence_day);
                    $pending_purchase_power_in_month = $this->get_power_in_month($pending_purchase_date);
                    $pending_purchase_power_in_year = $this->get_power_in_year($pending_purchase_date);

                    $a['pending_purchase_date'] = $pending_purchase_date;
                    $a['pending_purchase_day'] = $pending_purchase_day;
                    $a['pending_purchase_total_day'] = abs($diffDaysWithFirst);
                    $a['pending_purchase_sequence_day'] = $pending_purchase_sequence_day;
                    $a['pending_purchase_avg_sequence_day'] = round($pending_purchase_avg_sequence_day);
                    $a['pending_purchase_pressture_day'] = $pending_purchase_pressture_day;
                    $a['pending_purchase_power_in_month'] = $pending_purchase_power_in_month;
                    $a['pending_purchase_power_in_year'] = $pending_purchase_power_in_year;
                } else if ($order_status_id == 8) {
                    // $order_status = 'canceled';

                    $a['canceled_order_times'] = intval($orderProduct->canceled_order_times) + 1;
                    $a['canceled_order_discount_times'] = intval($orderProduct->canceled_order_discount_times) + $order_discount_times;
                    $a['canceled_order_free_delivery_times'] = intval($orderProduct->canceled_order_free_delivery_times) + $all_order_free_delivery_times;
                    $a['canceled_product_count'] = intval($orderProduct->canceled_product_count) + $count_selected;
                    $a['canceled_product_discount_count'] = intval($orderProduct->canceled_product_discount_count) + ($order_discount_times * $count_selected);
                    $a['canceled_product_free_delivery_count'] = intval($orderProduct->canceled_product_free_delivery_count) + ($all_order_free_delivery_times * $count_selected);
                    $a['canceled_total_product_pay_price'] = intval($orderProduct->canceled_total_product_pay_price) + round($sale_price * $count_selected);
                    $a['canceled_total_product_discount'] = intval($orderProduct->canceled_total_product_discount) + round($order_discount_times * $discount_price * $count_selected);
                    $a['canceled_avg_product_pay_price'] = round($a['canceled_total_product_pay_price'] / $a['canceled_product_count']);
                    $a['canceled_avg_product_discount'] = $a['canceled_product_discount_count'] > 0 ? round($a['canceled_total_product_discount'] / $a['canceled_product_discount_count']) : 0;
                    $a['canceled_total_order_delivery'] = intval($orderProduct->canceled_total_order_delivery) + round($delivery_price);
                    $a['canceled_total_product_delivery'] = intval($orderProduct->canceled_total_product_delivery) + round($delivery_price / $all_count_products);
                    $a['canceled_avg_product_delivery'] = ($a['canceled_product_count'] - $a['canceled_product_free_delivery_count']) > 0 ? round($a['canceled_total_product_delivery'] / ($a['canceled_product_count'] - $a['canceled_product_free_delivery_count'])) : 0;
                    $a['canceled_order_last_date'] = verta($order->created_at);

                    $fristDate = verta($orderProduct->canceled_order_first_date);
                    $lastDatelast = verta($orderProduct->canceled_order_last_date);
                    $lastDateNow = strval(verta($order->created_at));
                    $lastDate = SUBSTR($lastDateNow, 0, 10);
                    $diffDays = verta($lastDatelast)->diffDays($lastDateNow, false);
                    $diffDaysWithFirst = verta($lastDatelast)->diffDays($fristDate, false);

                    $canceled_purchase_date = array();
                    $orderProduct->canceled_purchase_date != null ? $canceled_purchase_date = json_decode($orderProduct->canceled_purchase_date) : null;
                    array_push($canceled_purchase_date, $lastDate);

                    $canceled_purchase_day = array();
                    $orderProduct->canceled_purchase_date != null ? $canceled_purchase_day = json_decode($orderProduct->canceled_purchase_day) : null;
                    array_push($canceled_purchase_day, abs($diffDaysWithFirst));

                    $canceled_purchase_sequence_day = array();
                    $orderProduct->canceled_purchase_date != null ? $canceled_purchase_sequence_day = json_decode($orderProduct->canceled_purchase_sequence_day) : null;
                    array_push($canceled_purchase_sequence_day, abs($diffDays));

                    $canceled_purchase_sequence_day = array_filter($canceled_purchase_sequence_day);
                    $canceled_purchase_avg_sequence_day = count($canceled_purchase_sequence_day) == 0 ? 0 : array_sum($canceled_purchase_sequence_day) / count($canceled_purchase_sequence_day);

                    $canceled_purchase_pressture_day = $this->get_pressture_day($canceled_purchase_sequence_day);
                    $canceled_purchase_power_in_month = $this->get_power_in_month($canceled_purchase_date);
                    $canceled_purchase_power_in_year = $this->get_power_in_year($canceled_purchase_date);

                    $a['canceled_purchase_date'] = $canceled_purchase_date;
                    $a['canceled_purchase_day'] = $canceled_purchase_day;
                    $a['canceled_purchase_total_day'] = abs($diffDaysWithFirst);
                    $a['canceled_purchase_sequence_day'] = $canceled_purchase_sequence_day;
                    $a['canceled_purchase_avg_sequence_day'] = round($canceled_purchase_avg_sequence_day);
                    $a['canceled_purchase_pressture_day'] = $canceled_purchase_pressture_day;
                    $a['canceled_purchase_power_in_month'] = $canceled_purchase_power_in_month;
                    $a['canceled_purchase_power_in_year'] = $canceled_purchase_power_in_year;
                } else if ($order_status_id == 14) {
                    // $order_status = 'returned';
                    $a['returned_order_times'] = intval($orderProduct->returned_order_times) + 1;
                    $a['returned_order_discount_times'] = intval($orderProduct->returned_order_discount_times) + $order_discount_times;
                    $a['returned_order_free_delivery_times'] = intval($orderProduct->returned_order_free_delivery_times) + $all_order_free_delivery_times;
                    $a['returned_product_count'] = intval($orderProduct->returned_product_count) + $count_selected;
                    $a['returned_product_discount_count'] = intval($orderProduct->returned_product_discount_count) + ($order_discount_times * $count_selected);
                    $a['returned_product_free_delivery_count'] = intval($orderProduct->returned_product_free_delivery_count) + ($all_order_free_delivery_times * $count_selected);
                    $a['returned_total_product_pay_price'] = intval($orderProduct->returned_total_product_pay_price) + round($sale_price * $count_selected);
                    $a['returned_total_product_discount'] = intval($orderProduct->returned_total_product_discount) + round($order_discount_times * $discount_price * $count_selected);
                    $a['returned_avg_product_pay_price'] = round($a['returned_total_product_pay_price'] / $a['returned_product_count']);
                    $a['returned_avg_product_discount'] = $a['returned_product_discount_count'] > 0 ? round($a['returned_total_product_discount'] / $a['returned_product_discount_count']) : 0;
                    $a['returned_total_order_delivery'] = intval($orderProduct->returned_total_order_delivery) + round($delivery_price);
                    $a['returned_total_product_delivery'] = intval($orderProduct->returned_total_product_delivery) + round($delivery_price / $all_count_products);
                    $a['returned_avg_product_delivery'] = ($a['returned_product_count'] - $a['returned_product_free_delivery_count']) > 0 ? round($a['returned_total_product_delivery'] / ($a['returned_product_count'] - $a['returned_product_free_delivery_count'])) : 0;
                    $a['returned_order_last_date'] = verta($order->created_at);

                    $fristDate = verta($orderProduct->returned_order_first_date);
                    $lastDatelast = verta($orderProduct->returned_order_last_date);
                    $lastDateNow = strval(verta($order->created_at));
                    $lastDate = SUBSTR($lastDateNow, 0, 10);
                    $diffDays = verta($lastDatelast)->diffDays($lastDateNow, false);
                    $diffDaysWithFirst = verta($lastDatelast)->diffDays($fristDate, false);

                    $returned_purchase_date = array();
                    $orderProduct->returned_purchase_date != null ? $returned_purchase_date = json_decode($orderProduct->returned_purchase_date) : null;
                    array_push($returned_purchase_date, $lastDate);

                    $returned_purchase_day = array();
                    $orderProduct->returned_purchase_date != null ? $returned_purchase_day = json_decode($orderProduct->returned_purchase_day) : null;
                    array_push($returned_purchase_day, abs($diffDaysWithFirst));

                    $returned_purchase_sequence_day = array();
                    $orderProduct->returned_purchase_date != null ? $returned_purchase_sequence_day = json_decode($orderProduct->returned_purchase_sequence_day) : null;
                    array_push($returned_purchase_sequence_day, abs($diffDays));

                    $returned_purchase_sequence_day = array_filter($returned_purchase_sequence_day);
                    $returned_purchase_avg_sequence_day = count($returned_purchase_sequence_day) == 0 ? 0 : array_sum($returned_purchase_sequence_day) / count($returned_purchase_sequence_day);

                    $returned_purchase_pressture_day = $this->get_pressture_day($returned_purchase_sequence_day);
                    $returned_purchase_power_in_month = $this->get_power_in_month($returned_purchase_date);
                    $returned_purchase_power_in_year = $this->get_power_in_year($returned_purchase_date);

                    $a['returned_purchase_date'] = $returned_purchase_date;
                    $a['returned_purchase_day'] = $returned_purchase_day;
                    $a['returned_purchase_total_day'] = abs($diffDaysWithFirst);
                    $a['returned_purchase_sequence_day'] = $returned_purchase_sequence_day;
                    $a['returned_purchase_avg_sequence_day'] = round($returned_purchase_avg_sequence_day);
                    $a['returned_purchase_pressture_day'] = $returned_purchase_pressture_day;
                    $a['returned_purchase_power_in_month'] = $returned_purchase_power_in_month;
                    $a['returned_purchase_power_in_year'] = $returned_purchase_power_in_year;
                } else {
                    // $order_status = 'delivered';
                    $a['delivered_order_times'] = intval($orderProduct->delivered_order_times) + 1;
                    $a['delivered_order_discount_times'] = intval($orderProduct->delivered_order_discount_times) + $order_discount_times;
                    $a['delivered_order_free_delivery_times'] = intval($orderProduct->delivered_order_free_delivery_times) + $all_order_free_delivery_times;
                    $a['delivered_product_count'] = intval($orderProduct->delivered_product_count) + $count_selected;
                    $a['delivered_product_discount_count'] = intval($orderProduct->delivered_product_discount_count) + ($order_discount_times * $count_selected);
                    $a['delivered_product_free_delivery_count'] = intval($orderProduct->delivered_product_free_delivery_count) + ($all_order_free_delivery_times * $count_selected);
                    $a['delivered_total_product_pay_price'] = intval($orderProduct->delivered_total_product_pay_price) + round($sale_price * $count_selected);
                    $a['delivered_total_product_discount'] = intval($orderProduct->delivered_total_product_discount) + round($order_discount_times * $discount_price * $count_selected);
                    $a['delivered_avg_product_pay_price'] = round($a['delivered_total_product_pay_price'] / $a['delivered_product_count']);
                    $a['delivered_avg_product_discount'] = $a['delivered_product_discount_count'] > 0 ? round($a['delivered_total_product_discount'] / $a['delivered_product_discount_count']) : 0;
                    $a['delivered_total_order_delivery'] = intval($orderProduct->delivered_total_order_delivery) + round($delivery_price);
                    $a['delivered_total_product_delivery'] = intval($orderProduct->delivered_total_product_delivery) + round($delivery_price / $all_count_products);
                    $a['delivered_avg_product_delivery'] = ($a['delivered_product_count'] - $a['delivered_product_free_delivery_count']) > 0 ? round($a['delivered_total_product_delivery'] / ($a['delivered_product_count'] - $a['delivered_product_free_delivery_count'])) : 0;
                    $a['delivered_order_last_date'] = verta($order->created_at);

                    $fristDate = verta($orderProduct->delivered_order_first_date);
                    $lastDatelast = verta($orderProduct->delivered_order_last_date);
                    $lastDateNow = strval(verta($order->created_at));
                    $lastDate = SUBSTR($lastDateNow, 0, 10);
                    $diffDays = verta($lastDatelast)->diffDays($lastDateNow, false);
                    $diffDaysWithFirst = verta($lastDatelast)->diffDays($fristDate, false);

                    $delivered_purchase_date = array();
                    $orderProduct->delivered_purchase_date != null ? $delivered_purchase_date = json_decode($orderProduct->delivered_purchase_date) : null;
                    array_push($delivered_purchase_date, $lastDate);

                    $delivered_purchase_day = array();
                    $orderProduct->delivered_purchase_date != null ? $delivered_purchase_day = json_decode($orderProduct->delivered_purchase_day) : null;
                    array_push($delivered_purchase_day, abs($diffDaysWithFirst));

                    $delivered_purchase_sequence_day = array();
                    $orderProduct->delivered_purchase_date != null ? $delivered_purchase_sequence_day = json_decode($orderProduct->delivered_purchase_sequence_day) : null;
                    array_push($delivered_purchase_sequence_day, abs($diffDays));

                    $delivered_purchase_sequence_day = array_filter($delivered_purchase_sequence_day);
                    $delivered_purchase_avg_sequence_day = count($delivered_purchase_sequence_day) == 0 ? 0 : array_sum($delivered_purchase_sequence_day) / count($delivered_purchase_sequence_day);

                    $delivered_purchase_pressture_day = $this->get_pressture_day($delivered_purchase_sequence_day);
                    $delivered_purchase_power_in_month = $this->get_power_in_month($delivered_purchase_date);
                    $delivered_purchase_power_in_year = $this->get_power_in_year($delivered_purchase_date);

                    $a['delivered_purchase_date'] = $delivered_purchase_date;
                    $a['delivered_purchase_day'] = $delivered_purchase_day;
                    $a['delivered_purchase_total_day'] = abs($diffDaysWithFirst);
                    $a['delivered_purchase_sequence_day'] = $delivered_purchase_sequence_day;
                    $a['delivered_purchase_avg_sequence_day'] = round($delivered_purchase_avg_sequence_day);
                    $a['delivered_purchase_pressture_day'] = $delivered_purchase_pressture_day;
                    $a['delivered_purchase_power_in_month'] = $delivered_purchase_power_in_month;
                    $a['delivered_purchase_power_in_year'] = $delivered_purchase_power_in_year;

                }

                HistoryCustomerOrderProduct::where('customer_id', $customer[$i]->id)->where('product_id', $product_id[$i])->update($a);
            } else {

                if ($order_status_id <= 5) {
                    $order_status = 'pending';
                } else if ($order_status_id == 8) {
                    $order_status = 'canceled';
                } else if ($order_status_id == 14) {
                    $order_status = 'returned';
                } else {
                    $order_status = 'delivered';
                }

                $all_purchase_power_in_month = $this->get_power_in_month([$order->created_at]);
                $all_purchase_power_in_year = $this->get_power_in_year([$order->created_at]);

                $a = array();
                $a['customer_id'] = $customer[$i]->id;
                $a['product_id'] = $product_id[$i];

                $a['all_order_times'] = 1;
                $a['all_order_discount_times'] = $order_discount_times;
                $a['all_order_free_delivery_times'] = $all_order_free_delivery_times;
                $a['all_product_count'] = $count_selected;
                $a['all_product_discount_count'] = $order_discount_times * $count_selected;
                $a['all_product_free_delivery_count'] = $all_order_free_delivery_times * $count_selected;
                $a['all_total_product_pay_price'] = round($sale_price * $count_selected);
                $a['all_total_product_discount'] = round($order_discount_times * $discount_price * $count_selected);
                $a['all_avg_product_pay_price'] = round($a['all_total_product_pay_price'] / $a['all_product_count']);
                $a['all_avg_product_discount'] = $order_discount_times > 0 ? round($a['all_total_product_discount'] / $a['all_product_discount_count']) : 0;
                $a['all_total_order_delivery'] = round($delivery_price);
                $a['all_total_product_delivery'] = round($delivery_price / $all_count_products);
                $a['all_avg_product_delivery'] = ($a['all_product_count'] - $a['all_product_free_delivery_count']) > 0 ? round($a['all_total_product_delivery'] / ($a['all_product_count'] - $a['all_product_free_delivery_count'])) : 0;
                $a['all_order_first_date'] = verta($order->created_at);
                $a['all_order_last_date'] = verta($order->created_at);
                $a['all_purchase_date'] = '["' . verta($order->created_at) . '"]';
                $a['all_purchase_day'] = '[0]';
                $a['all_purchase_total_day'] = 0;
                $a['all_purchase_sequence_day'] = '[]';
                $a['all_purchase_avg_sequence_day'] = 0;
                $a['all_purchase_pressture_day'] = 'first';
                $a['all_purchase_power_in_month'] = $all_purchase_power_in_month;
                $a['all_purchase_power_in_year'] = $all_purchase_power_in_year;

                $a['pending_order_times'] = $order_status == 'pending' ? 1 : 0;
                $a['pending_order_discount_times'] = $order_status == 'pending' ? $order_discount_times : 0;
                $a['pending_order_free_delivery_times'] = $order_status == 'pending' ? $all_order_free_delivery_times : 0;
                $a['pending_product_count'] = $order_status == 'pending' ? $count_selected : 0;
                $a['pending_product_discount_count'] = $order_status == 'pending' ? $order_discount_times * $count_selected : 0;
                $a['pending_product_free_delivery_count'] = $order_status == 'pending' ? $all_order_free_delivery_times * $count_selected : 0;
                $a['pending_total_product_pay_price'] = $order_status == 'pending' ? round($sale_price * $count_selected) : 0;
                $a['pending_total_product_discount'] = $order_status == 'pending' ? round($order_discount_times * $discount_price * $count_selected) : 0;
                $a['pending_avg_product_pay_price'] = $order_status == 'pending' ? round($a['pending_total_product_pay_price'] / $a['pending_product_count']) : 0;
                $a['pending_avg_product_discount'] = $order_status == 'pending' ? $order_discount_times > 0 ? round($a['pending_total_product_discount'] / $a['pending_product_discount_count']) : 0 : 0;
                $a['pending_total_order_delivery'] = $order_status == 'pending' ? round($delivery_price) : 0;
                $a['pending_total_product_delivery'] = $order_status == 'pending' ? round($delivery_price / $all_count_products) : 0;
                $a['pending_avg_product_delivery'] = $order_status == 'pending' ? ($a['pending_product_count'] - $a['pending_product_free_delivery_count']) > 0 ? round($a['pending_total_product_delivery'] / ($a['pending_product_count'] - $a['pending_product_free_delivery_count'])) : 0 : 0;
                $a['pending_order_first_date'] = $order_status == 'pending' ? verta($order->created_at) : null;
                $a['pending_order_last_date'] = $order_status == 'pending' ? verta($order->created_at) : null;
                $a['pending_purchase_date'] = $order_status == 'pending' ? '["' . verta($order->created_at) . '"]' : '[]';
                $a['pending_purchase_day'] = $order_status == 'pending' ? '[0]' : '[]';
                $a['pending_purchase_total_day'] = 0;
                $a['pending_purchase_sequence_day'] = '[]';
                $a['pending_purchase_avg_sequence_day'] = 0;
                $a['pending_purchase_pressture_day'] = $order_status == 'pending' ? 'first' : null;
                $a['pending_purchase_power_in_month'] = $order_status == 'pending' ? $all_purchase_power_in_month : null;
                $a['pending_purchase_power_in_year'] = $order_status == 'pending' ? $all_purchase_power_in_year : null;

                $a['delivered_order_times'] = $order_status == 'delivered' ? 1 : 0;
                $a['delivered_order_discount_times'] = $order_status == 'delivered' ? $order_discount_times : 0;
                $a['delivered_order_free_delivery_times'] = $order_status == 'delivered' ? $all_order_free_delivery_times : 0;
                $a['delivered_product_count'] = $order_status == 'delivered' ? $count_selected : 0;
                $a['delivered_product_discount_count'] = $order_status == 'delivered' ? $order_discount_times * $count_selected : 0;
                $a['delivered_product_free_delivery_count'] = $order_status == 'delivered' ? $all_order_free_delivery_times * $count_selected : 0;
                $a['delivered_total_product_pay_price'] = $order_status == 'delivered' ? round($sale_price * $count_selected) : 0;
                $a['delivered_total_product_discount'] = $order_status == 'delivered' ? round($order_discount_times * $discount_price * $count_selected) : 0;
                $a['delivered_avg_product_pay_price'] = $order_status == 'delivered' ? round($a['delivered_total_product_pay_price'] / $a['delivered_product_count']) : 0;
                $a['delivered_avg_product_discount'] = $order_status == 'delivered' ? $order_discount_times > 0 ? round($a['delivered_total_product_discount'] / $a['delivered_product_discount_count']) : 0 : 0;
                $a['delivered_total_order_delivery'] = $order_status == 'delivered' ? round($delivery_price) : 0;
                $a['delivered_total_product_delivery'] = $order_status == 'delivered' ? round($delivery_price / $all_count_products) : 0;
                $a['delivered_avg_product_delivery'] = $order_status == 'delivered' ? ($a['delivered_product_count'] - $a['delivered_product_free_delivery_count']) > 0 ? round($a['delivered_total_product_delivery'] / ($a['delivered_product_count'] - $a['delivered_product_free_delivery_count'])) : 0 : 0;
                $a['delivered_order_first_date'] = $order_status == 'delivered' ? verta($order->created_at) : null;
                $a['delivered_order_last_date'] = $order_status == 'delivered' ? verta($order->created_at) : null;
                $a['delivered_purchase_date'] = $order_status == 'delivered' ? '["' . verta($order->created_at) . '"]' : '[]';
                $a['delivered_purchase_day'] = $order_status == 'delivered' ? '[0]' : '[]';
                $a['delivered_purchase_total_day'] = 0;
                $a['delivered_purchase_sequence_day'] = '[]';
                $a['delivered_purchase_avg_sequence_day'] = 0;
                $a['delivered_purchase_pressture_day'] = $order_status == 'delivered' ? 'first' : null;
                $a['delivered_purchase_power_in_month'] = $order_status == 'delivered' ? $all_purchase_power_in_month : null;
                $a['delivered_purchase_power_in_year'] = $order_status == 'delivered' ? $all_purchase_power_in_year : null;

                $a['returned_order_times'] = $order_status == 'returned' ? 1 : 0;
                $a['returned_order_discount_times'] = $order_status == 'returned' ? $order_discount_times : 0;
                $a['returned_order_free_delivery_times'] = $order_status == 'returned' ? $all_order_free_delivery_times : 0;
                $a['returned_product_count'] = $order_status == 'returned' ? $count_selected : 0;
                $a['returned_product_discount_count'] = $order_status == 'returned' ? $order_discount_times * $count_selected : 0;
                $a['returned_product_free_delivery_count'] = $order_status == 'returned' ? $all_order_free_delivery_times * $count_selected : 0;
                $a['returned_total_product_pay_price'] = $order_status == 'returned' ? round($sale_price * $count_selected) : 0;
                $a['returned_total_product_discount'] = $order_status == 'returned' ? round($order_discount_times * $discount_price * $count_selected) : 0;
                $a['returned_avg_product_pay_price'] = $order_status == 'returned' ? round($a['returned_total_product_pay_price'] / $a['returned_product_count']) : 0;
                $a['returned_avg_product_discount'] = $order_status == 'returned' ? $order_discount_times > 0 ? round($a['returned_total_product_discount'] / $a['returned_product_discount_count']) : 0 : 0;
                $a['returned_total_order_delivery'] = $order_status == 'returned' ? round($delivery_price) : 0;
                $a['returned_total_product_delivery'] = $order_status == 'returned' ? round($delivery_price / $all_count_products) : 0;
                $a['returned_avg_product_delivery'] = $order_status == 'returned' ? ($a['returned_product_count'] - $a['returned_product_free_delivery_count']) > 0 ? round($a['returned_total_product_delivery'] / ($a['returned_product_count'] - $a['returned_product_free_delivery_count'])) : 0 : 0;
                $a['returned_order_first_date'] = $order_status == 'returned' ? verta($order->created_at) : null;
                $a['returned_order_last_date'] = $order_status == 'returned' ? verta($order->created_at) : null;
                $a['returned_purchase_date'] = $order_status == 'returned' ? '["' . verta($order->created_at) . '"]' : '[]';
                $a['returned_purchase_day'] = $order_status == 'returned' ? '[0]' : '[]';
                $a['returned_purchase_total_day'] = 0;
                $a['returned_purchase_sequence_day'] = '[]';
                $a['returned_purchase_avg_sequence_day'] = 0;
                $a['returned_purchase_pressture_day'] = $order_status == 'returned' ? 'first' : null;
                $a['returned_purchase_power_in_month'] = $order_status == 'returned' ? $all_purchase_power_in_month : null;
                $a['returned_purchase_power_in_year'] = $order_status == 'returned' ? $all_purchase_power_in_year : null;

                $a['canceled_order_times'] = $order_status == 'canceled' ? 1 : 0;
                $a['canceled_order_discount_times'] = $order_status == 'canceled' ? $order_discount_times : 0;
                $a['canceled_order_free_delivery_times'] = $order_status == 'canceled' ? $all_order_free_delivery_times : 0;
                $a['canceled_product_count'] = $order_status == 'canceled' ? $count_selected : 0;
                $a['canceled_product_discount_count'] = $order_status == 'canceled' ? $order_discount_times * $count_selected : 0;
                $a['canceled_product_free_delivery_count'] = $order_status == 'canceled' ? $all_order_free_delivery_times * $count_selected : 0;
                $a['canceled_total_product_pay_price'] = $order_status == 'canceled' ? round($sale_price * $count_selected) : 0;
                $a['canceled_total_product_discount'] = $order_status == 'canceled' ? round($order_discount_times * $discount_price * $count_selected) : 0;
                $a['canceled_avg_product_pay_price'] = $order_status == 'canceled' ? round($a['canceled_total_product_pay_price'] / $a['canceled_product_count']) : 0;
                $a['canceled_avg_product_discount'] = $order_status == 'canceled' ? $order_discount_times > 0 ? round($a['canceled_total_product_discount'] / $a['canceled_product_discount_count']) : 0 : 0;
                $a['canceled_total_order_delivery'] = $order_status == 'canceled' ? round($delivery_price) : 0;
                $a['canceled_total_product_delivery'] = $order_status == 'canceled' ? round($delivery_price / $all_count_products) : 0;
                $a['canceled_avg_product_delivery'] = $order_status == 'canceled' ? ($a['canceled_product_count'] - $a['canceled_product_free_delivery_count']) > 0 ? round($a['canceled_total_product_delivery'] / ($a['canceled_product_count'] - $a['canceled_product_free_delivery_count'])) : 0 : 0;
                $a['canceled_order_first_date'] = $order_status == 'canceled' ? verta($order->created_at) : null;
                $a['canceled_order_last_date'] = $order_status == 'canceled' ? verta($order->created_at) : null;
                $a['canceled_purchase_date'] = $order_status == 'canceled' ? '["' . verta($order->created_at) . '"]' : '[]';
                $a['canceled_purchase_day'] = $order_status == 'canceled' ? '[0]' : '[]';
                $a['canceled_purchase_total_day'] = 0;
                $a['canceled_purchase_sequence_day'] = '[]';
                $a['canceled_purchase_avg_sequence_day'] = 0;
                $a['canceled_purchase_pressture_day'] = $order_status == 'canceled' ? 'first' : null;
                $a['canceled_purchase_power_in_month'] = $order_status == 'canceled' ? $all_purchase_power_in_month : null;
                $a['canceled_purchase_power_in_year'] = $order_status == 'canceled' ? $all_purchase_power_in_year : null;

                HistoryCustomerOrderProduct::create($a);
            }
        }
        $hc_status['hc_order_product_status'] = 1;
        Order::where('id', $order->id)->update($hc_status);
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

        $Order = Order::where('customer_id', $input['customer_id'])->whereIn('order_status_id', $order_status_id)->get();

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

        $id = $input['id'];

        Helper::DBConnection(env('SERVER_STATUS', '') . 'utopia_store_' . $input['idb']);

        $Order = Order::where('id', $id)->first();

        if (Order::where('id', $id)->exists()) {
            $Order = $Order->update([
                'status' => $input['status'],
            ]);
        }

        return $Order;
    }

}
