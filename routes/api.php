<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AuthenticationController;

Route::post('/login-api', [AuthenticationController::class, 'loginApi']);
Route::post('/register-api', [AuthenticationController::class, 'registerApi']);

Route::get('/products', [ProductController::class, 'getProductListApi']);

Route::middleware('auth:api')->group(function () {
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::put('/cart/{id}', [CartController::class, 'update']);
    Route::delete('/cart/{id}', [CartController::class, 'destroy']);
});

Route::middleware('auth:api')->group(function () {
    Route::get('/orders', [OrderController::class, 'apiIndex']);
    Route::get('/orders/{id}', [OrderController::class, 'apiShow']);
    Route::post('/checkout', [OrderController::class, 'checkout']);
});

Route::post('/payment/callback', [PaymentController::class, 'callback']);

Route::middleware('auth:api')->group(function () {
    Route::get('/notifications/check', [NotificationController::class, 'checkNew']);
});

Route::middleware('auth:api')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'apiIndex']);
    Route::put('/notifications/{id}/read', [NotificationController::class, 'apiMarkAsRead']);
    Route::delete('/notifications/{id}', [NotificationController::class, 'apiDestroy']);
});

Route::prefix('buyer/password')->group(function () {
    Route::post('/request', [AuthenticationController::class, 'requestResetCode']);
    Route::post('/reset', [AuthenticationController::class, 'resetPasswordWithCode']);
});
