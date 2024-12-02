<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\api\CustomerController;
use App\Http\Controllers\api\InventoryController;
use App\Http\Controllers\api\ProductController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\api\ReorderRequestController;
use App\Http\Controllers\api\SalesController;
use App\Http\Controllers\api\StoreCartController;
use App\Http\Controllers\api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::get('/user', [UserController::class, 'index']);
Route::post('/user', [UserController::class, 'store'])->name('user.store');
Route::post('/login', [AuthController::class, 'login'])->name('user.login');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::get('/profile/show',  [ProfileController::class, 'show']);

    Route::controller(UserController::class)->group(function () {
        Route::get('/user/{id}',                'show');
        Route::put('/user/{id}',                'updateUserAccount')->name('user.update');
        Route::put('/user/password/{id}',       'password')->name('user.password');
        Route::delete('/user/{id}',             'destroy');
    });

    Route::controller(CustomerController::class)->group(function () {
        Route::get('/customer',                     'index');
        Route::post('/customer',                    'store');
        Route::get('/customer/{id}',                'show');
        Route::put('/user/isFrequentShopper/{id}',  'isFrequentShopper')->name('user.isFrequentShopper');
        Route::put('/user/password/{id}',           'password')->name('user.password');
        Route::put('/customer/{id}',                'updateCustomerAccount');
        Route::delete('/customer/{id}',             'destroy');
    });

    Route::controller(InventoryController::class)->group(function () {
        Route::get('/inventory/all',                'index');
        Route::get('/store/inventory',              'storeInventoryIndex');
        Route::post('/inventory',                   'store');
        Route::get('/inventory/{id}',               'show');
        Route::put('/inventory/{id}',               'update');
        Route::delete('/inventory/{id}',            'destroy');
    });

    Route::controller(ProductController::class)->group(function () {
        Route::get('/product/all',                  'index');
        Route::get('/vendor/product',               'vendorProductIndex');
        Route::post('/product',                     'store');
        Route::get('/product/{id}',                 'show');
        Route::put('/product/{id}',                 'update');
        Route::delete('/product/{id}',              'destroy');
    });

    Route::controller(ReorderRequestController::class)->group(function () {
        Route::get('/reorder-request',              'index');
        Route::get('/user/reorder-request',         'userReorderIndex');
        Route::post('/reorder-request',             'store');
        Route::get('/reorder-request/{id}',         'show');
        Route::put('/reorder-request/{id}',            'update');
        Route::put('/reorder-quantity/{id}',        'updateQuantity');
        Route::delete('/reorder-request/{id}',      'destroy');
    });

    Route::controller(SalesController::class)->group(function () {
        Route::get('/sales',                        'index');
        Route::get('/store/sales',                  'storeSaleIndex');
        Route::post('/sales',                       'store');
        Route::get('/sales/{id}',                   'show');
        Route::put('/sales/{id}',                   'update');
        Route::delete('/sales/{id}',                'destroy');
    });

    Route::controller(StoreCartController::class)->group(function () {
        Route::get('/store-cart',                 'index');
        Route::get('/store-cart',                 'storeCartIndex');
        Route::post('/store-cart',                'store');
        Route::get('/store-cart/{id}',             'show');
        Route::put('/store-cart/{id}',             'update');
        Route::delete('/store-cart/{id}',          'destroy');
    });
});
