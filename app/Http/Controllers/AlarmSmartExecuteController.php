<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\AlarmSmartCategory;
use App\Models\AlarmSmartExecute;
use App\Models\AlarmSmartOfferCategory;
use App\Models\AlarmSmartOfferCustomer;
use App\Models\AlarmSmartOfferProduct;
use App\Models\AlarmStatus;
use App\Models\AnalyticsCustomer;
use App\Models\Customer;
use App\Models\DiscountStatus;
use App\Models\HistoryCustomerLike;
use App\Models\HistoryCustomerNextCart;
use App\Models\HistoryCustomerOrderProduct;
use App\Models\Product;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Request;

class AlarmSmartExecuteController extends Controller
{
    public function getExecuteListAlarmByStatus()
    {

        $input = Request()->all();

        Helper::DBConnection(env('SERVER_STATUS', '') . 'utopia_store_' . $input['idb']);

        // $AlarmSmartExecute['head_list'] = array();
        if ($input['alarm_status_id'] == 0 && $input['alarm_smart_category_id'] == 0) {
            $AlarmSmartExecute = AlarmSmartExecute::get();
        } else if ($input['alarm_smart_category_id'] == 0) {
            $AlarmSmartExecute = AlarmSmartExecute::where('alarm_status_id', $input['alarm_status_id'])->get();
        } else if ($input['alarm_status_id'] == 0) {
            $AlarmSmartExecute = AlarmSmartExecute::where('alarm_smart_category_id', $input['alarm_smart_category_id'])->get();
        } else {
            $AlarmSmartExecute = AlarmSmartExecute::where('alarm_smart_category_id', $input['alarm_smart_category_id'])->where('alarm_status_id', $input['alarm_status_id'])->get();
        }

        // dd($AlarmSmartExecute);
        for ($i = 0; $i < count($AlarmSmartExecute); $i++) {

            $AlarmSmartExecute[$i]['alarm_smart_category_name'] = AlarmSmartCategory::where('id', $AlarmSmartExecute[$i]->alarm_smart_category_id)->first()->name_fa;

            $DateNow = now()->toJalali();

            // $now = str_replace('-0', '-', strval(substr($DateNow, 0, 10)));
            // $a = '[' . str_replace('-', ',', $now) . ']';
            // $b = json_decode($a);
            // $dateSpella = Verta::jalaliToGregorian($b[0], $b[1], $b[2]);

            $executeday = $AlarmSmartExecute[$i]->date;
            // $i==0 ? dd($customerId ,$customer,$executeday) : null;
            $day = str_replace('-0', '-', $executeday);
            $c = '[' . str_replace('-', ',', $day) . ']';
            $d = json_decode($c);
            $dateSpellc = Verta::jalaliToGregorian($d[0], $d[1], $d[2]);
            $ExecuteDate = $dateSpellc != null ? $dateSpellc[0] . '-' . $dateSpellc[1] . '-' . $dateSpellc[2] . ' ' . $AlarmSmartExecute[$i]->setting_send_time : '';

            $diffDays = verta($ExecuteDate)->diffdays($DateNow, false);

            $diffHours = verta($ExecuteDate)->diffHours($DateNow, false);

            // $i == 0 ? dd(strval(verta($ExecuteDate)), strval($DateNow), $diffDays, $diffHours) : null;

            $AlarmSmartExecute[$i]['diff_days'] = round($diffDays);
            $AlarmSmartExecute[$i]['diff_hours'] = round($diffHours);

            $AlarmStatus = AlarmStatus::get();
            $AlarmSmartExecute[$i]['alarm_status_name'] = $AlarmStatus->where('id', $AlarmSmartExecute[$i]['alarm_status_id'])->first()->name_fa;

            $DiscountStatus = DiscountStatus::get();
            $AlarmSmartExecute[$i]['setting_discount_status_name'] = $DiscountStatus->where('id', $AlarmSmartExecute[$i]['setting_discount_status_id'])->first()->name_fa;
            // dd(gettype($AlarmSmartExecute));
            $AlarmSmartExecute[$i]['customer_offer_list'] = array();

            // dd($AlarmSmartExecute[$i]->id);
            $AlarmSmartExecute[$i]['customer_offer_list'] = AlarmSmartOfferCustomer::where('alarm_smart_execute_id', $AlarmSmartExecute[$i]->id)->get();

            for ($j = 0; $j < count($AlarmSmartExecute[$i]['customer_offer_list']); $j++) {
                $customer_id = $AlarmSmartExecute[$i]['customer_offer_list'][$j]['customer_id'];

                $customer = Customer::where('id', $customer_id)->first();
                $AlarmSmartExecute[$i]['customer_offer_list'][$j]['customer_name'] = $customer->name . ' ' . $customer->family;
                $AlarmSmartExecute[$i]['customer_offer_list'][$j]['customer_gender'] = $customer->gender == 'male' ? 'آقای ' : ($customer->gender == 'female' ? 'خانم ' : '');
                $AlarmSmartExecute[$i]['customer_offer_list'][$j]['customer_job'] = 'معلم';
                $AlarmSmartExecute[$i]['customer_offer_list'][$j]['customer_interest'] = 'ورزش';

                $DiscountStatus = DiscountStatus::get();
                $AlarmSmartExecute[$i]['customer_offer_list'][$j]['setting_discount_status_name'] = $DiscountStatus->where('id', $AlarmSmartExecute[$i]['customer_offer_list'][$j]['setting_discount_status_id'])->first()->name_fa;

                $birthday = $customer->birth;
                // $i==0 ? dd($customerId ,$customer,$Birthday) : null;
                $birth = str_replace('-0', '-', $birthday);
                $e = '[' . str_replace('-', ',', $birth) . ']';
                $g = json_decode($e);
                $dateSpelld = Verta::jalaliToGregorian($g[0], $g[1], $g[2]);
                $BirthDate = $dateSpelld != null ? $dateSpelld[0] . '-' . $dateSpelld[1] . '-' . $dateSpelld[2] : '';

                $diffYearAge = verta($BirthDate)->diffYears($DateNow, false);

                $AlarmSmartExecute[$i]['customer_offer_list'][$j]['customer_age'] = abs($diffYearAge);

                $AlarmSmartExecute[$i]['customer_offer_list'][$j]['alarm_status_name'] = $AlarmStatus->where('id', $AlarmSmartExecute[$i]['customer_offer_list'][$j]['alarm_status_id'])->first()->name_fa;
            }

            // dd($AlarmSmartExecute);
        }

        // dd(count($AlarmSmartExecute[0]['customer_offer_list']));

        // dd($a);
        // dd($AlarmSmartExecute);
        // echo($AlarmSmartExecute);
        return $AlarmSmartExecute;
    }
    public function setBirthExecuteList()
    {
        $input = Request()->all();

        Helper::DBConnection(env('SERVER_STATUS', '') . 'utopia_store_' . $input['idb']);

        $alarm_smart_category = AlarmSmartCategory::where('name', 'birthday')->first();
        $delay_day_execute = $alarm_smart_category->delay_day_execute;

        $e = AlarmSmartOfferCustomer::select('customer_id')->distinct()->where('alarm_smart_execute_id', 1)->where('alarm_status_id', 1)->get()->toArray();

        $categoryIdCookedArray = array();
        for ($i = 0; $i < count($e); $i++) {
            array_push($categoryIdCookedArray, $e[$i]['customer_id']);
        }

        $h = Customer::where('birth', '!=', '')->get();

        $customer_id_array = array();
        for ($i = 0; $i < count($h); $i++) {
            array_push($customer_id_array, $h[$i]->id);
        }

        $diff_customer_id_array = array_values(array_diff($customer_id_array, $categoryIdCookedArray));
        // dd($diff_customer_id_array);

        $customer_array = array();
        $DateNow = now()->toJalali();
        $AlarmSmartExecuteId = 0;

        for ($i = 0; $i < count($diff_customer_id_array); $i++) {
            try {
                $customerId = $diff_customer_id_array[$i];
                // dd($customerId);
                $customer = Customer::where('id', $customerId)->first();
                // $i==1 ? dd($customerId ,$customer) : null;
                $dataOffer = array();
                // $AlarmSmartExecuteId = 0;

                $now = str_replace('-0', '-', strval(substr($DateNow, 0, 10)));
                $a = '[' . str_replace('-', ',', $now) . ']';
                $b = json_decode($a);
                $dateSpella = Verta::jalaliToGregorian($b[0], $b[1], $b[2]);

                $birthday = $customer->birth;
                // $i==0 ? dd($customerId ,$customer,$birthday) : null;
                $birth = str_replace('-0', '-', $birthday);
                $c = '[' . str_replace('-', ',', $birth) . ']';
                $d = json_decode($c);
                $dateSpellc = Verta::jalaliToGregorian($d[0], $d[1], $d[2]);
                $BirthDate = $dateSpellc != null ? $dateSpella[0] . '-' . $dateSpellc[1] . '-' . $dateSpellc[2] : '';

                $diffDays = verta($BirthDate)->diffDays($DateNow, false);

                // $i == 1 ? dd($DateNow, $BirthDate, $diffDays, $delay_day_execute, $diffDays < 0 && abs($diffDays) <= $delay_day_execute) : null;

                if ($diffDays < 0 && abs($diffDays) <= $delay_day_execute) {

                    $analytics_customer = AnalyticsCustomer::where('customer_id', $customerId)->orderBy('score', 'desc')->get();

                    // $i == 0 ? dd($customerId, $analytics_customer) : null;

                    if ($analytics_customer != null && count($analytics_customer) > 0) {
                        unset($customer['password']);
                        unset($customer['status']);
                        unset($customer['mobile_verified_at']);
                        unset($customer['email_verified_at']);
                        unset($customer['remember_token']);
                        array_push($customer_array, $customer);

                        $data = array();
                        $data['alarm_smart_category_id'] = $alarm_smart_category->id;
                        $data['alarm_status_id'] = 1;
                        $data['date'] = strval(substr($DateNow, 0, 10));
                        // $data['date'] = strval(substr($DateNow, 0, 10)) . ' ' . $alarm_smart_category->send_time;
                        $data['setting_send_time'] = $alarm_smart_category->send_time;
                        $data['setting_discount'] = $alarm_smart_category->discount_tag;
                        $data['setting_product_count'] = $alarm_smart_category->product_count;
                        $data['setting_category_count'] = $alarm_smart_category->category_count;
                        $data['setting_sms'] = $alarm_smart_category->send_sms;
                        $data['setting_notification'] = $alarm_smart_category->send_notification;
                        $data['setting_email'] = $alarm_smart_category->send_email;

                        if ($AlarmSmartExecuteId == 0) {
                            $AlarmSmartExecute = AlarmSmartExecute::create($data);
                            $AlarmSmartExecuteId = $AlarmSmartExecute->id;
                        }

                        $dataOffer['alarm_smart_execute_id'] = $AlarmSmartExecuteId;
                        // $dataOffer['alarm_smart_execute_id'] = 1;
                        $dataOffer['alarm_status_id'] = 1;
                        $dataOffer['customer_id'] = $customerId;
                        $dataOffer['alarm_smart_category_id'] = $alarm_smart_category->id;

                        $dataOffer['setting_send_date'] = strval(substr($DateNow, 0, 10));
                        $dataOffer['setting_send_time'] = $alarm_smart_category->send_time;
                        $dataOffer['setting_discount'] = $alarm_smart_category->discount_tag;
                        $dataOffer['setting_product_count'] = $alarm_smart_category->product_count;
                        $dataOffer['setting_category_count'] = $alarm_smart_category->category_count;
                        $dataOffer['setting_sms'] = $alarm_smart_category->send_sms;
                        $dataOffer['setting_notification'] = $alarm_smart_category->send_notification;
                        $dataOffer['setting_email'] = $alarm_smart_category->send_email;

                        $count_analytics = count($analytics_customer);
                        for ($j = 0; $j < 3; $j++) {
                            $array_name = $j == 0 ? 'one' : ($j == 1 ? 'two' : 'tree');
                            // dd($j ,$array_name ,$analytics_customer[$j]->product_id);
                            if ($count_analytics > 0) {
                                $Product = Product::where('id', $analytics_customer[$j]->product_id)->first();
                                $ASOfferData = array();
                                $ASOfferData['customer_id'] = $customerId;
                                $ASOfferData['product_id'] = $analytics_customer[$j]->product_id;
                                $ASOfferData['category_id'] = json_decode($Product->categories_id)[0];

                                if ($dataOffer['setting_discount'] == 'safe') {
                                    $ASOfferData['product_discount'] = 'safe';
                                    $ASOfferData['product_discount_precentage'] = $Product->safe_discount_percent;
                                } else if ($dataOffer['setting_discount'] == 'special') {
                                    $ASOfferData['product_discount'] = 'special';
                                    $ASOfferData['product_discount_precentage'] = $Product->special_discount_percent;
                                } else if ($dataOffer['setting_discount'] == 'exceptional') {
                                    $ASOfferData['product_discount'] = 'exceptional';
                                    $ASOfferData['product_discount_precentage'] = $Product->exceptional_discount_percent;
                                } else {
                                    $ASOfferData['product_discount'] = 'none';
                                    $ASOfferData['product_discount_precentage'] = 0;
                                }

                                $ASOfferData['product_discription'] = '';
                                $AlarmSmartOfferProduct = AlarmSmartOfferProduct::Create($ASOfferData);
                                $dataOffer['as_product_id_' . $array_name] = $AlarmSmartOfferProduct->id;
                            } else {

                                $product_id_array = HistoryCustomerOrderProduct::select('product_id')->distinct()->get();

                                if ($product_id_array != null && count($product_id_array) >= 1) {

                                    if (count($product_id_array) == 1) {
                                        if ($j == 1) {
                                            $dataOffer['as_product_id_two'] = $analytics_customer[0]->product_id;
                                            $dataOffer['as_product_id_tree'] = $product_id_array[0];
                                            $count_analytics--;
                                        } else if ($j == 2) {
                                            $dataOffer['as_product_id_tree'] = $product_id_array[0];
                                        }
                                        $count_analytics--;
                                    } else {
                                        // I am not sure yet for this code plaese test after
                                        $sum_purchase_count_array = array();

                                        for ($k = 0; $k < count($product_id_array); $k++) {
                                            $HistoryCustomerOrderProduct = HistoryCustomerOrderProduct::where('product_id', $product_id_array[$k])->orderBy('id', 'desc')->limit(100)->get();
                                            array_push($sum_purchase_count_array, $HistoryCustomerOrderProduct->sum('all_product_count'));
                                        }

                                        if ($j == 1) {
                                            $product_count_max = max($sum_purchase_count_array);
                                            $key = array_keys($sum_purchase_count_array, $product_count_max)[0];

                                            $dataOffer['as_product_id_two'] = $product_id_array[$key];

                                            unset($sum_purchase_count_array[$key]);
                                            unset($product_id_array[$key]);

                                            $product_count_max = max($sum_purchase_count_array);
                                            $key = array_keys($sum_purchase_count_array, $product_count_max)[0];

                                            $dataOffer['as_product_id_tree'] = $product_id_array[$key];
                                            $count_analytics--;
                                        } else if ($j == 2) {
                                            $product_count_max = max($sum_purchase_count_array);
                                            $key = array_keys($sum_purchase_count_array, $product_count_max)[0];

                                            $dataOffer['as_product_id_tree'] = $product_id_array[$key];
                                        }
                                        $count_analytics--;
                                    }

                                } else {
                                    if ($j == 1) {
                                        $dataOffer['as_product_id_two'] = $analytics_customer[0]->product_id;
                                        $dataOffer['as_product_id_tree'] = $analytics_customer[0]->product_id;
                                        $count_analytics--;
                                    } else if ($j == 2) {
                                        $dataOffer['as_product_id_tree'] = $analytics_customer[0]->product_id;
                                    }
                                    $count_analytics--;
                                }
                            }
                            $count_analytics--;
                        }

                        $analytics_customer = AnalyticsCustomer::select('category_id')->where('customer_id', $customerId)->distinct()->get();

                        if ($analytics_customer != null && count($analytics_customer) > 0) {
                            $count_analytics = count($analytics_customer);
                            for ($j = 0; $j < 3; $j++) {
                                $array_name = $j == 0 ? 'one' : ($j == 1 ? 'two' : 'tree');
                                // dd($j ,$array_name ,$analytics_customer[$j]->product_id) ;
                                if ($count_analytics > 0) {
                                    // $Category = Category::where('id', $analytics_customer[$j]->category_id)->first();
                                    $ASOfferData = array();
                                    $ASOfferData['customer_id'] = $customerId;
                                    $ASOfferData['category_id'] = $analytics_customer[$j]->category_id;

                                    if ($dataOffer['setting_discount'] == 'safe') {
                                        $ASOfferData['category_discount'] = 'safe';
                                        $ASOfferData['category_discount_precentage'] = 0.55;
                                    } else if ($dataOffer['setting_discount'] == 'special') {
                                        $ASOfferData['category_discount'] = 'special';
                                        $ASOfferData['category_discount_precentage'] = 0.25;
                                    } else if ($dataOffer['setting_discount'] == 'exceptional') {
                                        $ASOfferData['category_discount'] = 'exceptional';
                                        $ASOfferData['category_discount_precentage'] = 0.15;
                                    } else {
                                        $ASOfferData['category_discount'] = 'none';
                                        $ASOfferData['category_discount_precentage'] = 0;
                                    }

                                    $ASOfferData['category_discription'] = '';
                                    $AlarmSmartOfferCategory = AlarmSmartOfferCategory::Create($ASOfferData);
                                    $dataOffer['as_category_id_' . $array_name] = $AlarmSmartOfferCategory->id;
                                } else {
                                    $dataOffer['as_category_id_' . $array_name] = $analytics_customer[0]->category_id;
                                    // $category_id_array = HistoryCustomerOrderProduct::select('category_id')->distinct()->get();

                                    // if ($category_id_array != null && count($category_id_array) >= 1) {

                                    //     if (count($category_id_array) == 1) {
                                    //         if ($j == 1) {
                                    //             $dataOffer['as_category_id_two'] = $analytics_customer[0]->category_id;
                                    //             $dataOffer['as_category_id_tree'] = $category_id_array[0];
                                    //             $count_analytics--;
                                    //         } else if ($j == 2) {
                                    //             $dataOffer['as_category_id_tree'] = $category_id_array[0];
                                    //         }
                                    //         $count_analytics--;
                                    //     } else {
                                    //         // I am not sure yet for this code plaese test after
                                    //         $sum_purchase_count_array = array();

                                    //         for ($k = 0; $k < count($category_id_array); $k++) {
                                    //             $HistoryCustomerOrdercategory = HistoryCustomerOrderProduct::where('category_id', $category_id_array[$k])->orderBy('id', 'desc')->limit(100)->get();
                                    //             array_push($sum_purchase_count_array, $HistoryCustomerOrdercategory->sum('all_category_count'));
                                    //         }

                                    //         if ($j == 1) {
                                    //             $category_count_max = max($sum_purchase_count_array);
                                    //             $key = array_keys($sum_purchase_count_array, $category_count_max)[0];

                                    //             $dataOffer['as_category_id_two'] = $category_id_array[$key];

                                    //             unset($sum_purchase_count_array[$key]);
                                    //             unset($category_id_array[$key]);

                                    //             $category_count_max = max($sum_purchase_count_array);
                                    //             $key = array_keys($sum_purchase_count_array, $category_count_max)[0];

                                    //             $dataOffer['as_category_id_tree'] = $category_id_array[$key];
                                    //             $count_analytics--;
                                    //         } else if ($j == 2) {
                                    //             $category_count_max = max($sum_purchase_count_array);
                                    //             $key = array_keys($sum_purchase_count_array, $category_count_max)[0];

                                    //             $dataOffer['as_category_id_tree'] = $category_id_array[$key];
                                    //         }
                                    //         $count_analytics--;
                                    //     }

                                    // } else {
                                    //     if ($j == 1) {
                                    //         $dataOffer['as_category_id_two'] = $analytics_customer[0]->category_id;
                                    //         $dataOffer['as_category_id_tree'] = $analytics_customer[0]->category_id;
                                    //         $count_analytics--;
                                    //     } else if ($j == 2) {
                                    //         $dataOffer['as_category_id_tree'] = $analytics_customer[0]->category_id;
                                    //     }
                                    //     $count_analytics--;
                                    // }
                                }
                                $count_analytics--;
                            }
                        }

                        // else{} after computing
                    }
                }

            } catch (\Throwable $th) {
                throw $th;
            }
            // $i == 3 ? dd($customerId ,$dataOffer) : null;
            if (count($dataOffer) > 0) {
                AlarmSmartOfferCustomer::create($dataOffer);
            }

        }

        dd($customer_array);
    }

    public function setLikeExecuteList()
    {
        $input = Request()->all();

        Helper::DBConnection(env('SERVER_STATUS', '') . 'utopia_store_' . $input['idb']);

        $a = AlarmSmartOfferCustomer::select('customer_id')->distinct()->where('alarm_smart_execute_id', 6)->where('alarm_status_id', 1)->get()->toArray();

        $categoryIdCookedArray = array();
        for ($i = 0; $i < count($a); $i++) {
            array_push($categoryIdCookedArray, $a[$i]['customer_id']);
        }

        // dd($categoryIdCookedArray);
        $b = HistoryCustomerLike::select('customer_id')->distinct()->get()->toArray();

        $customer_id_array = array();
        for ($i = 0; $i < count($b); $i++) {
            array_push($customer_id_array, $b[$i]['customer_id']);
        }

        // dd($customer_id_array);
        $diff_customer_id_array = array_values(array_diff($customer_id_array, $categoryIdCookedArray));
        // dd($diff_customer_id_array);

        $customer_array = array();
        $DateNow = now()->toJalali();

        for ($i = 0; $i < count($diff_customer_id_array); $i++) {
            $index = count($customer_array);
            $customer_array[$index]['customer_id'] = $diff_customer_id_array[$i];

            $likeProduct = HistoryCustomerLike::select('product_id')->distinct()->where('customer_id', $diff_customer_id_array[$i])->get()->toArray();

            // dd($likeProduct);
            $informationLikeArray = array();
            for ($j = 0; $j < count($likeProduct); $j++) {
                $like = HistoryCustomerLike::where('product_id', $likeProduct[$j]['product_id'])->where('customer_id', $diff_customer_id_array[$i])->get();

                $c = array();
                $c['product_id'] = $likeProduct[$j]['product_id'];
                $c['count_like'] = count($like);
                $c['last_like'] = $like->last()->toArray()['like'];

                array_push($informationLikeArray, $c);

            }
            // $i == 0 ? dd($informationLikeArray) : null;
            // dd($c);
            if(count($informationLikeArray) == 0 ){
                continue;
            }
            array_push($customer_array[$index], $informationLikeArray);

        }

        return $customer_array;

    }

    public function setNextCartExecuteList()
    {
        $input = Request()->all();

        Helper::DBConnection(env('SERVER_STATUS', '') . 'utopia_store_' . $input['idb']);

        $a = AlarmSmartOfferCustomer::select('customer_id')->distinct()->where('alarm_smart_execute_id', 6)->where('alarm_status_id', 1)->get()->toArray();

        $categoryIdCookedArray = array();
        for ($i = 0; $i < count($a); $i++) {
            array_push($categoryIdCookedArray, $a[$i]['customer_id']);
        }

        // dd($categoryIdCookedArray);
        $b = HistoryCustomerNextCart::select('customer_id')->distinct()->get()->toArray();

        $customer_id_array = array();
        for ($i = 0; $i < count($b); $i++) {
            array_push($customer_id_array, $b[$i]['customer_id']);
        }

        // dd($customer_id_array);
        $diff_customer_id_array = array_values(array_diff($customer_id_array, $categoryIdCookedArray));
        // dd($diff_customer_id_array);

        $customer_array = array();
        $DateNow = now()->toJalali();

        for ($i = 0; $i < count($diff_customer_id_array); $i++) {
            $index = count($customer_array);
            $customer_array[$index]['customer_id'] = $diff_customer_id_array[$i];

            $likeProduct = HistoryCustomerNextCart::select('product_id')->distinct()->where('customer_id', $diff_customer_id_array[$i])->get()->toArray();

            // dd($likeProduct);
            $informationLikeArray = array();
            for ($j = 0; $j < count($likeProduct); $j++) {
                $next_cart = HistoryCustomerNextCart::where('product_id', $likeProduct[$j]['product_id'])->where('customer_id', $diff_customer_id_array[$i])->get();
                // dd($next_cart);
                $c = array();
                $c['product_id'] = $likeProduct[$j]['product_id'];
                $c['count_next_cart'] = count($next_cart);
                $c['last_increment_decrement'] = $next_cart->last()->toArray()['increment_decrement'];
                $c['last_tab'] = $next_cart->last()->toArray()['tab'];

                array_push($informationLikeArray, $c);

            }
            // $i == 0 ? dd($informationLikeArray) : null;
            // dd($c);
            if(count($informationLikeArray) == 0 ){
                continue;
            }
            array_push($customer_array[$index], $informationLikeArray);

        }

        return $customer_array;

    }

}
