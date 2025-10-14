<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\CartController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\PaymentController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::post('auth/register', [AuthController::class, 'register'])->middleware('throttle:6,1');
    Route::post('auth/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/{slug}', [ProductController::class, 'show']);

   // Autenticado
   Route::middleware('auth:sanctum')->group(function () {
       Route::get('me', [AuthController::class, 'me']);
       Route::post('auth/logout', [AuthController::class, 'logout']); //to test

       // Produtos (apenas admin)
       Route::middleware('can:manage-products')->group(function () {
           Route::post('products', [ProductController::class, 'store']);
           Route::put('products/{product}', [ProductController::class, 'update']);
           Route::delete('products/{product}', [ProductController::class, 'destroy']);
       });

       // Carrinho
       Route::get('cart', [CartController::class, 'show']);
       Route::post('cart/items', [CartController::class, 'addItem']);
       Route::patch('cart/items/{itemId}', [CartController::class, 'updateItem']);
       Route::delete('cart/items/{itemId}', [CartController::class, 'removeItem']);
       Route::delete('cart', [CartController::class, 'clear']);

       // Checkout & Orders
       Route::post('checkout/confirm', [OrderController::class, 'confirm']);
       Route::get('orders', [OrderController::class, 'index']);
       Route::get('orders/{order}', [OrderController::class, 'show'])->middleware('can:view,order');

       // Payments
       Route::post('orders/{order}/payments/init', [PaymentController::class, 'init'])->middleware('can:update,order');
       Route::post('payments/webhook/{provider}', [PaymentController::class, 'webhook'])->withoutMiddleware('auth:sanctum');
   });
   
});