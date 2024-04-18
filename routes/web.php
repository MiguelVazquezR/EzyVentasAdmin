<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\GlobalProductController;
use App\Http\Controllers\SuscriptionController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return Inertia::render('Welcome', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//     ]);
// });

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


// suscriptions routes-----------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------------
Route::resource('suscriptions', SuscriptionController::class)->middleware('web');
Route::get('suscriptions-get-by-page/{currentPage}', [SuscriptionController::class, 'getItemsByPage'])->name('suscriptions.get-by-page')->middleware('auth');
Route::get('suscriptions-get-matches/{query}', [SuscriptionController::class, 'getMatches'])->name('suscriptions.get-matches');
Route::get('suscriptions-get-filters/{prop}/{value}', [SuscriptionController::class, 'getFilters'])->name('suscriptions.get-filters');


//Global products routes (Catálgo base)----------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------
Route::resource('global-products', GlobalProductController::class)->middleware('auth');
Route::get('global-products-search', [GlobalProductController::class, 'searchProduct'])->name('global-products.search')->middleware('auth');
Route::post('global-products/update-with-media/{global_product}', [GlobalProductController::class, 'updateWithMedia'])->name('global-products.update-with-media')->middleware('auth');
// Route::get('global-products-select', [GlobalProductController::class, 'selectGlobalProducts'])->name('global-products.select')->middleware('auth');
Route::get('global-products-get-by-page/{currentPage}', [GlobalProductController::class, 'getItemsByPage'])->name('global-products.get-by-page')->middleware('auth');
Route::get('global-products-fetch-product-info/{global_product_id}', [GlobalProductController::class, 'fetchProductInfo'])->name('global-products.fetch-info-product')->middleware('auth');
Route::get('global-products-filter', [GlobalProductController::class, 'filter'])->name('global-products.filter')->middleware('auth');


//categories routes--------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------
Route::resource('categories', CategoryController::class)->middleware('auth');


//brands routes------------------------------------------------------------------------------------------  
//-------------------------------------------------------------------------------------------------------
Route::resource('brands', BrandController::class)->middleware('auth');
