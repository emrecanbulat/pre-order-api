<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\V1\Rest\CartController;
use App\Http\Controllers\Api\V1\Rest\OrderController;
use App\Http\Controllers\Api\V1\Rest\ProductController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login'])->name('login');

Route::get('product', [ProductController::class, 'index']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('cart', [CartController::class, 'addToCart']);
    Route::get('cart', [CartController::class, 'index']);
    Route::delete('cart', [CartController::class, 'delete']);

    Route::post('order', [OrderController::class, 'makeOrder']);
});
