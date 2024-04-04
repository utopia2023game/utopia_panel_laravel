<?php

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\ControllersManagement\Controller as ControllersManagementController;

Route::get('/migrate_management_by_database_name', [ControllersManagementController::class, 'migrateManagementByDataBaseName'])->name('migrateManagementByDataBaseName');
Route::get('/transfer_data_from_mangment_to_customer', [ControllersManagementController::class, 'transferDataFromMangmentToCustomer'])->name('transferDataFromMangmentToCustomer');

Route::get('/rollback_by_database_name', [Controller::class, 'rollBackByDataBaseName'])->name('rollBackByDataBaseName');
Route::get('/migrate_by_database_name', [Controller::class, 'migrateByDataBaseName'])->name('migrateByDataBaseName');
Route::get('/migrate_fresh_by_database_name', [Controller::class, 'migrateFreshByDataBaseName'])->name('migrateFreshByDataBaseName');

Route::get('/rollback_all_database', [Controller::class, 'rollBackAllDataBase'])->name('rollBackAllDataBase');
Route::get('/migrate_all_database', [Controller::class, 'migrateAllDataBase'])->name('migrateAllDataBase');
Route::get('/migrate_status_all_database', [Controller::class, 'migrateStatusAllDataBase'])->name('migrateStatusAllDataBase');
Route::get('/migrate_fresh_all_database', [Controller::class, 'migrateFreshAllDataBase'])->name('migrateFreshAllDataBase');

Route::get('/category_store', [Controller::class, 'categoryStore'])->name('categoryStore');







// Route::post('/media_create', [MediaController::class, 'create'])->name('create');
// Route::get('/media_update', [MediaController::class, 'update'])->name('update');
// Route::get('/media_softdelete', [MediaController::class, 'softdelete'])->name('softdelete');
// Route::get('/media_forcedelete', [MediaController::class, 'forcedelete'])->name('forcedelete');
// Route::get('/media_restore', [MediaController::class, 'restore'])->name('restore');
// Route::get('/media_list', [MediaController::class, 'listMedias'])->name('listMedias');