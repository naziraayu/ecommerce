<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthenticationController;

Route::post('/login-api', [AuthenticationController::class, 'loginApi']);

Route::get('/products', [ProductController::class, 'getProductListApi']);

Route::middleware('auth:api')->get('/cart', function () {
    $user = Auth::user();
    return response()->json([
        'success' => true,
        'data' => $user->carts
    ]);
});

Route::middleware('auth:api')->post('/checkout', [OrderController::class, 'checkout']);

