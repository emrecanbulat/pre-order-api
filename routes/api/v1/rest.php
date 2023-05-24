<?php

use App\Http\Controllers\Api\V1\Rest\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('product', [ProductController::class, 'index']);
