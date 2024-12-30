<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\api\CartController;
use App\Http\Controllers\api\CustomerController;
use App\Http\Controllers\api\InventoryController;
use App\Http\Controllers\api\OrderController;
use App\Http\Controllers\api\ProductController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\api\ReorderRequestController;
use App\Http\Controllers\api\SalesController;
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

Route::post('/customer', [CustomerController::class, 'store']);
Route::post('/user', [UserController::class, 'store'])->name('user.store');
Route::post('/login', [AuthController::class, 'login'])->name('user.login');

Route::get('/product/all', [ProductController::class, 'index']);
Route::get('/product/shop/all', [ProductController::class, 'listtAllProducts']);
Route::get('/user', [UserController::class, 'index']);
Route::get('/inventory/all', [InventoryController::class, 'index']);

// cart api routes
Route::post('/carts/add', [CartController::class, 'addItem']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::get('/profile/show',  [ProfileController::class, 'show']);
    Route::get('/carts/by-store', [CartController::class, 'getCart']);
    Route::delete('/carts/item/{itemId}', [CartController::class, 'removeItem']);
    Route::post('/carts/{action}/{itemId}', [CartController::class, 'updateQuantity']);
    Route::get('/carts/storeIndex', [CartController::class, 'cartStoreIndex']);
    Route::get('/carts/show/{id}', [CartController::class, 'cartShow']);
    Route::put('/carts/{id}/update-status', [CartController::class, 'updateCartStatus']);

    Route::controller(UserController::class)->group(function () {
        Route::get('/user/{id}',                'show');
        Route::put('/user/{id}',                'updateUserAccount')->name('user.update');
        Route::put('/user/password/{id}',       'password')->name('user.password');
        Route::delete('/user/{id}',             'destroy');
    });

    Route::controller(CustomerController::class)->group(function () {
        Route::get('/customer',                     'index');
        Route::get('/customer/{id}',                'show');
        Route::put('/user/isFrequentShopper/{id}',  'isFrequentShopper')->name('user.isFrequentShopper');
        Route::put('/user/password/{id}',           'password')->name('user.password');
        Route::put('/customer/{id}',                'updateCustomerAccount');
        Route::delete('/customer/{id}',             'destroy');
    });

    Route::controller(InventoryController::class)->group(function () {
        Route::get('/store/inventory',              'storeInventoryIndex');
        Route::get('/inventory/store/all',          'storeInventoryall');
        Route::post('/inventory',                   'store');
        Route::get('/inventory/{id}',               'show');
        Route::put('/inventory/{id}',               'update');
        Route::put('/inventory/status/{id}',        'updateStatus');
        Route::delete('/inventory/{id}',            'destroy');
    });

    Route::controller(ProductController::class)->group(function () {
        Route::get('/vendor/product',               'vendorProductIndex');
        Route::post('/product',                     'store');
        Route::get('/product/{id}',                 'show');
        Route::put('/product/details/{id}',         'updateDetails');
        Route::put('/product-status/{id}',          'updateStatus');
        Route::put('/product-isActive/{id}',        'updateIsActive');
        Route::delete('/product/{id}',              'destroy');
    });

    Route::controller(ReorderRequestController::class)->group(function () {
        Route::get('/reorder-request',              'index');
        Route::get('/reorder-request/user',         'userReorderIndex');
        Route::post('/reorder-request',             'store');
        Route::get('/reorder-request/{id}',         'show');
        Route::put('/reorder-request/{id}',         'update');
        Route::put('/reorder-quantity/{id}',        'updateQuantity');
        Route::delete('/reorder-request/{id}',      'destroy');
    });

    Route::controller(SalesController::class)->group(function () {
        Route::get('/sales',                        'index');
        Route::get('/sales/store',                  'storeSaleIndex');
        Route::post('/sales',                       'store');
        Route::get('/sales/{id}',                   'show');
        Route::put('/sales/{id}',                   'update');
        Route::delete('/sales/{id}',                'destroy');
    });

    Route::controller(OrderController::class)->group(function () {
        Route::get('/order',                        'index');
        Route::get('/order/user',                   'userOrderIndex');
        Route::post('/order',                       'store');
        Route::get('/order/{id}',                   'show');
        Route::put('/order-status/{id}',            'updateStatus');
        Route::delete('/order/{id}',                'destroy');
    });
});
