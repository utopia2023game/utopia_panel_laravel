<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BankController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\OrderStatusController;
use App\Http\Controllers\HistoryCustomerController;
use App\Http\Controllers\AlarmSmartExecuteController;
use App\Http\Controllers\AnalyticsCustomerController;
use App\Http\Controllers\FinancialCustomerController;
use App\Http\Controllers\AlarmSmartCategoryController;
use App\Http\Controllers\FinancialCustomerRankController;
use App\Http\Controllers\AlarmSmartOfferCustomerController;
use App\Http\Controllers\HistoryCustomerOrderProductController;
use App\Http\ControllersManagement\Controller as ManagementController;
use App\Http\ControllersManagement\CategoryController as ManagementCategoryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



Route::post('get_list_category', [ManagementCategoryController::class, 'getListCategory'])->name('getListCategory');
Route::post('create_new_management_db_and_migrate', [ManagementController::class, 'createNewManagementDbAndMigrate'])->name('createNewManagementDbAndMigrate');
Route::post('input_check', [ManagementController::class, 'inputCheck'])->name('inputCheck');

Route::post('product_create',[ProductController::class ,'create']);
Route::post('product_list', [ProductController::class, 'listProducts'])->name('listProducts');
Route::post('list_products_cart', [ProductController::class, 'listProductsCart'])->name('listProductsCart');
Route::post('product_update', [ProductController::class, 'update'])->name('update');
Route::post('product_softdelete', [ProductController::class, 'softdelete'])->name('softdelete');
Route::post('product_forcedelete', [ProductController::class, 'forcedelete'])->name('forcedelete');
Route::post('product_restore', [ProductController::class, 'restore'])->name('restore');
Route::post('product_data', [ProductController::class, 'ProductData'])->name('ProductData');
Route::post('set_page_view_product', [ProductController::class, 'setPageViewProduct'])->name('setPageViewProduct');


Route::post('category_create', [CategoryController::class, 'create'])->name('create');
Route::post('category_update', [CategoryController::class, 'update'])->name('update');
Route::post('category_softdelete', [CategoryController::class, 'softdelete'])->name('softdelete');
Route::post('category_forcedelete', [CategoryController::class, 'forcedelete'])->name('forcedelete');
Route::post('category_restore', [CategoryController::class, 'restore'])->name('restore');
Route::post('category_list', [CategoryController::class, 'listCategory'])->name('listCategory');
Route::post('category_children_list_by_cat_id', [CategoryController::class, 'categoryChildrenListByCatId'])->name('categoryChildrenListByCatId');
Route::post('category_add_remove_image', [CategoryController::class, 'categoryAddRemoveImage'])->name('categoryAddRemoveImage');

Route::post('set_hc_category', [HistoryCustomerController::class, 'setHcCategory'])->name('setHcCategory');
Route::post('set_hc_cart', [HistoryCustomerController::class, 'setHcCart'])->name('setHcCart');
Route::post('set_hc_next_cart', [HistoryCustomerController::class, 'setHcNextCart'])->name('setHcNextCart');
Route::post('set_hc_view', [HistoryCustomerController::class, 'setHcView'])->name('setHcView');
Route::post('set_hc_like', [HistoryCustomerController::class, 'setHcLike'])->name('setHcLike');
Route::post('set_hc_share', [HistoryCustomerController::class, 'setHcShare'])->name('setHcShare');
Route::post('set_hc_user', [HistoryCustomerController::class, 'setHcUser'])->name('setHcUser');


Route::post('create_new_customer', [CustomerController::class, 'createNewCustomer'])->name('createNewCustomer');
Route::post('check_customer_and_send_mobile_verify_code', [CustomerController::class, 'checkCustomerAndSendMobileVerifyCode'])->name('checkCustomerAndSendMobileVerifyCode');
Route::post('login_customer_mobile_password', [CustomerController::class, 'loginMobilePassword'])->name('loginMobilePassword');

Route::post('create_store_with_database_and_migrate', [Controller::class, 'createNewStoreWithDbAndMigrate'])->name('createNewStoreWithDbAndMigrate');
Route::post('check_store_and_send_mobile_verify_code', [Controller::class, 'checkStoreAndSendMobileVerifyCode'])->name('checkStoreAndSendMobileVerifyCode');
Route::post('login_mobile_password', [Controller::class, 'loginMobilePassword'])->name('loginMobilePassword');
Route::post('login_overview_with_password', [Controller::class, 'loginMobileOverViewPasswordIdb'])->name('loginMobileOverViewPasswordIdb');

Route::post('store_list', [StoreController::class, 'storeList'])->name('storeList');

Route::post('list_order_status', [OrderStatusController::class, 'listOrdersStatus'])->name('listOrdersStatus');
Route::post('order_product_table_refresh_all', [HistoryCustomerOrderProductController::class, 'set_hc_order_product_table_refresh_all'])->name('set_hc_order_product_table_refresh_all');

Route::post('send_order_cart', [OrderController::class, 'sendOrderCart'])->name('sendOrderCart');
Route::post('list_order', [OrderController::class, 'listOrders'])->name('listOrders');
Route::post('list_order_management', [OrderController::class, 'listOrdersManagement'])->name('listOrdersManagement');
Route::post('change_status', [OrderController::class, 'changeStatus'])->name('changeStatus');

Route::post('message_create', [MessageController::class, 'MessageCreate'])->name('MessageCreate');
Route::post('message_update', [MessageController::class, 'updateMessage'])->name('updateMessage');
Route::post('list_messages', [MessageController::class, 'listMessages'])->name('listMessages');
Route::post('message_soft_delete', [MessageController::class, 'softdelete'])->name('softdelete');
Route::post('message_force_delete', [MessageController::class, 'forcedelete'])->name('forcedelete');
Route::post('message_restore', [MessageController::class, 'restore'])->name('restore');
Route::post('set_page_view_message', [MessageController::class, 'setPageViewMessage'])->name('setPageViewMessage');
Route::post('confirm_message', [MessageController::class, 'confirmMessage'])->name('confirmMessage');

Route::post('address_create', [AddressController::class, 'AddressCreate'])->name('AddressCreate');
Route::post('address_update', [AddressController::class, 'updateAddress'])->name('updateAddress');
Route::post('list_address', [AddressController::class, 'listAddresss'])->name('listAddresss');
Route::post('data_address_by_priority', [AddressController::class, 'dataAddressByPriority'])->name('dataAddressByPriority');
Route::post('address_soft_delete', [AddressController::class, 'softdelete'])->name('softdelete');
Route::post('address_force_delete', [AddressController::class, 'forcedelete'])->name('forcedelete');
Route::post('address_restore', [AddressController::class, 'restore'])->name('restore');
Route::post('set_priority_address', [AddressController::class, 'setPriorityAddress'])->name('setPriorityAddress');
Route::post('confirm_address', [AddressController::class, 'confirmAddress'])->name('confirmAddress');
Route::post('confirm_message', [MessageController::class, 'confirmMessage'])->name('confirmMessage');

Route::post('set_birth_execute_list', [AlarmSmartExecuteController::class, 'setBirthExecuteList'])->name('setBirthExecuteList');
Route::post('set_like_execute_list', [AlarmSmartExecuteController::class, 'setLikeExecuteList'])->name('setLikeExecuteList');
Route::post('set_next_cart_execute_list', [AlarmSmartExecuteController::class, 'setNextCartExecuteList'])->name('setNextCartExecuteList');
Route::post('get_execute_list_alarm_smart_by_status', [AlarmSmartExecuteController::class, 'getExecuteListAlarmByStatus'])->name('getExecuteListAlarmByStatus');

Route::post('list_alarm_category', [AlarmSmartCategoryController::class, 'listAlarmSmartCategory'])->name('listAlarmSmartCategory');

Route::post('set_financial_customer', [FinancialCustomerController::class, 'setFinancialCustomer'])->name('setFinancialCustomer');
Route::post('set_financial_customer_rank', [FinancialCustomerRankController::class, 'setFinancialCustomerRank'])->name('setFinancialCustomerRank');

Route::post('get_offer_customer_information', [AlarmSmartOfferCustomerController::class, 'getOfferCustomerInformation'])->name('getOfferCustomerInformation');
Route::post('set_alarm_smart_offer_product_category_status', [AlarmSmartOfferCustomerController::class, 'setAlarmSmartOfferProductCategoryStatus'])->name('setAlarmSmartOfferProductCategoryStatus');

Route::post('customer_perfermance_computing_oprations', [AnalyticsCustomerController::class, 'customerPerfermanceComputingOprations'])->name('customerPerfermanceComputingOprations');

Route::post('delivery_create', [DeliveryController::class, 'DeliveryCreate'])->name('DeliveryCreate');
Route::post('delivery_update', [DeliveryController::class, 'updateDelivery'])->name('updateDelivery');
Route::post('list_delivery', [DeliveryController::class, 'listDeliverys'])->name('listDeliverys');
Route::post('data_delivery_by_priority', [DeliveryController::class, 'dataDeliveryByPriority'])->name('dataDeliveryByPriority');
Route::post('delivery_soft_delete', [DeliveryController::class, 'softdelete'])->name('softdelete');
Route::post('delivery_force_delete', [DeliveryController::class, 'forcedelete'])->name('forcedelete');
Route::post('delivery_restore', [DeliveryController::class, 'restore'])->name('restore');
Route::post('set_priority_delivery', [DeliveryController::class, 'setPriorityDelivery'])->name('setPriorityDelivery');
Route::post('confirm_delivery', [DeliveryController::class, 'confirmDelivery'])->name('confirmDelivery');

Route::post('bank_create', [BankController::class, 'BankCreate'])->name('BankCreate');
Route::post('bank_update', [BankController::class, 'updateBank'])->name('updateBank');
Route::post('list_bank', [BankController::class, 'listBanks'])->name('listBanks');
Route::post('data_bank_by_priority', [BankController::class, 'dataBankByPriority'])->name('dataBankByPriority');
Route::post('bank_soft_delete', [BankController::class, 'softdelete'])->name('softdelete');
Route::post('bank_force_delete', [BankController::class, 'forcedelete'])->name('forcedelete');
Route::post('bank_restore', [BankController::class, 'restore'])->name('restore');
Route::post('set_priority_bank', [BankController::class, 'setPriorityBank'])->name('setPriorityBank');
Route::post('confirm_bank', [BankController::class, 'confirmBank'])->name('confirmBank');

Route::post('set_images_slider', [SliderController::class, 'setImagesSlider'])->name('setImagesSlider');
Route::post('list_sliders', [SliderController::class, 'listSliders'])->name('listSliders');
Route::post('slider_soft_delete', [SliderController::class, 'softdelete'])->name('softdelete');
Route::post('slider_force_delete', [SliderController::class, 'forcedelete'])->name('forcedelete');
Route::post('slider_restore', [SliderController::class, 'restore'])->name('restore');


Route::post('comment_create',[CommentController::class ,'create'])->name('create');
Route::post('comment_update', [CommentController::class, 'update'])->name('update');
Route::post('comments_list',[CommentController::class ,'list'])->name('list');
Route::post('confirm_comment',[CommentController::class ,'confirmComment'])->name('confirmComment');
Route::post('comment_soft_delete', [CommentController::class, 'softdelete'])->name('softdelete');
Route::post('comment_force_delete', [CommentController::class, 'forcedelete'])->name('forcedelete');
Route::post('comment_restore', [CommentController::class, 'restore'])->name('restore');
Route::post('set_like_comment', [CommentController::class, 'setLikeComment'])->name('setLikeComment');
Route::post('set_response_comment', [CommentController::class, 'setResponseComment'])->name('setResponseComment');


// Route::post('media_create',[MediaController::class ,'create']);
