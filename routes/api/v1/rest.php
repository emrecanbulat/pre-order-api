<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\V1\Rest\ProductController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login'])->name('login');

Route::get('product', [ProductController::class, 'index']);


