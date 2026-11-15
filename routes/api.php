<?php

use App\Http\Controllers\Api\AdminAuthController;
use App\Http\Controllers\Api\CategoriesController;
use App\Http\Controllers\Api\ProductsController;
use App\Http\Controllers\ToppingsController;
use Illuminate\Support\Facades\Route;

Route::post('/admin/login', [AdminAuthController::class, 'login']);

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/admin/me', [AdminAuthController::class, 'me']);
    Route::get('/admin/dashboard', [AdminAuthController::class, 'dashboard']);
    Route::post('/admin/logout', [AdminAuthController::class, 'logout']);

    Route::get('/categories', [CategoriesController::class, 'index']);
    Route::post('/categories', [CategoriesController::class, 'store']);
    Route::get('/categories/{category}', [CategoriesController::class, 'show']);
    Route::put('/categories/{category}', [CategoriesController::class, 'update']);
    Route::delete('/categories/{category}', [CategoriesController::class, 'destroy']);

    Route::get('/products', [ProductsController::class, 'index']);
    Route::post('/products', [ProductsController::class, 'store']);
    Route::get('/products/{product}', [ProductsController::class, 'show']);
    Route::put('/products/{product}', [ProductsController::class, 'update']);
    Route::delete('/products/{product}', [ProductsController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/toppings', [ToppingsController::class, 'index']);
    Route::post('/toppings', [ToppingsController::class, 'store']);
    Route::put('/toppings/{topping}', [ToppingsController::class, 'update']);
    Route::delete('/toppings/{topping}', [ToppingsController::class, 'destroy']);
});
