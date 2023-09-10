<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SliderController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;

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

Route::post('product_create',[ProductController::class ,'create']);
Route::post('product_list', [ProductController::class, 'listProducts'])->name('listProducts');
Route::post('list_products_cart', [ProductController::class, 'listProductsCart'])->name('listProductsCart');
Route::post('product_update', [ProductController::class, 'update'])->name('update');
Route::post('product_softdelete', [ProductController::class, 'softdelete'])->name('softdelete');
Route::post('product_forcedelete', [ProductController::class, 'forcedelete'])->name('forcedelete');
Route::post('product_restore', [ProductController::class, 'restore'])->name('restore');
Route::post('product_data', [ProductController::class, 'ProductData'])->name('ProductData');
Route::post('set_visit_product', [ProductController::class, 'setVisitProduct'])->name('setVisitProduct');


Route::post('category_create', [CategoryController::class, 'create'])->name('create');
Route::post('category_update', [CategoryController::class, 'update'])->name('update');
Route::post('category_softdelete', [CategoryController::class, 'softdelete'])->name('softdelete');
Route::post('category_forcedelete', [CategoryController::class, 'forcedelete'])->name('forcedelete');
Route::post('category_restore', [CategoryController::class, 'restore'])->name('restore');
Route::post('category_list', [CategoryController::class, 'listCategory'])->name('listCategory');
Route::post('category_children_list_by_cat_id', [CategoryController::class, 'categoryChildrenListByCatId'])->name('categoryChildrenListByCatId');
Route::post('category_add_remove_image', [CategoryController::class, 'categoryAddRemoveImage'])->name('categoryAddRemoveImage');


Route::post('create_new_customer', [CustomerController::class, 'createNewCustomer'])->name('createNewCustomer');
Route::post('check_customer_and_send_mobile_verify_code', [CustomerController::class, 'checkCustomerAndSendMobileVerifyCode'])->name('checkCustomerAndSendMobileVerifyCode');
Route::post('login_customer_mobile_password', [CustomerController::class, 'loginMobilePassword'])->name('loginMobilePassword');

Route::post('create_store_with_database_and_migrate', [Controller::class, 'createNewStoreWithDbAndMigrate'])->name('createNewStoreWithDbAndMigrate');
Route::post('check_store_and_send_mobile_verify_code', [Controller::class, 'checkStoreAndSendMobileVerifyCode'])->name('checkStoreAndSendMobileVerifyCode');
Route::post('login_mobile_password', [Controller::class, 'loginMobilePassword'])->name('loginMobilePassword');
Route::post('login_overview_with_password', [Controller::class, 'loginMobileOverViewPasswordIdb'])->name('loginMobileOverViewPasswordIdb');
Route::post('store_list', [StoreController::class, 'storeList'])->name('storeList');




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
