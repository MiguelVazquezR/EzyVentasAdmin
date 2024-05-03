<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\GlobalProductController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SupportReportController;
use App\Http\Controllers\StoreController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::redirect('/', 'login');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});

// Admin routes-----------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------
Route::resource('admins', AdminController::class)->middleware('web');
Route::get('admins-get-notifications', [AdminController::class, 'getNotifications'])->middleware('auth')->name('admins.get-notifications');
Route::post('admins-read-notifications', [AdminController::class, 'readNotifications'])->middleware('auth')->name('admins.read-user-notifications');
Route::post('admins-delete-notifications', [AdminController::class, 'deleteNotifications'])->middleware('auth')->name('admins.delete-user-notifications');
Route::get('admins-get-by-page/{currentPage}', [AdminController::class, 'getItemsByPage'])->name('admins.get-by-page')->middleware('auth');
Route::get('admins-get-matches/{query}', [AdminController::class, 'getMatches'])->name('admins.get-matches');
Route::post('admins/update-with-media/{admin}', [AdminController::class, 'updateWithMedia'])->name('admins.update-with-media')->middleware('auth');


// stores routes-----------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------------
Route::resource('stores', StoreController::class)->middleware('web');
Route::get('stores-get-by-page/{currentPage}', [StoreController::class, 'getItemsByPage'])->name('stores.get-by-page')->middleware('auth');
Route::get('stores-get-matches/{query}', [StoreController::class, 'getMatches'])->name('stores.get-matches');
Route::get('stores-get-filters/{prop}/{value}', [StoreController::class, 'getFilters'])->name('stores.get-filters');


//Global products routes (Catálgo base)----------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------
Route::resource('global-products', GlobalProductController::class)->middleware('auth');
Route::get('global-products-search', [GlobalProductController::class, 'searchProduct'])->name('global-products.search')->middleware('auth');
Route::post('global-products/update-with-media/{global_product}', [GlobalProductController::class, 'updateWithMedia'])->name('global-products.update-with-media')->middleware('auth');
Route::get('global-products-get-by-page/{currentPage}', [GlobalProductController::class, 'getItemsByPage'])->name('global-products.get-by-page')->middleware('auth');
Route::get('global-products-fetch-product-info/{global_product_id}', [GlobalProductController::class, 'fetchProductInfo'])->name('global-products.fetch-info-product')->middleware('auth');
Route::get('global-products-filter', [GlobalProductController::class, 'filter'])->name('global-products.filter')->middleware('auth');


//categories routes--------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------
Route::resource('categories', CategoryController::class)->middleware('auth');


//brands routes------------------------------------------------------------------------------------------  
//-------------------------------------------------------------------------------------------------------
Route::resource('brands', BrandController::class)->middleware('auth');


//Payments routes------------------------------------------------------------------------------------------  
//---------------------------------------------------------------------------------------------------------
Route::resource('payments', PaymentController::class)->middleware('auth');
Route::put('payments/update-status/{payment}', [PaymentController::class, 'updateStatus'])->name('payments.update-status')->middleware('auth');


//support-reports routes------------------------------------------------------------------------------------------  
//---------------------------------------------------------------------------------------------------------
Route::resource('support-reports', SupportReportController::class)->middleware('auth');
Route::get('support-reports-get-by-page/{currentPage}', [SupportReportController::class, 'getItemsByPage'])->name('support-reports.get-by-page')->middleware('auth');
Route::get('support-reports-get-matches/{query}', [SupportReportController::class, 'getMatches'])->name('support-reports.get-matches');
Route::get('support-reports-get-matches/{query}', [SupportReportController::class, 'getMatches'])->name('support-reports.get-matches');
Route::put('support-reports-change-status/{support_report}/{status}', [SupportReportController::class, 'changeStatus'])->name('support-reports.change-status');
Route::get('support-reports-get-by-page/{currentPage}', [SupportReportController::class, 'getItemsByPage'])->name('support-reports.get-by-page')->middleware('auth');