<?php

use App\Http\Controllers\ToppingsController;
use Illuminate\Support\Facades\Route;

Route::get('/toppings', [ToppingsController::class, 'index']);
Route::post('/toppings', [ToppingsController::class, 'store']);
Route::put('/toppings/{topping}', [ToppingsController::class, 'update']);
Route::delete('/toppings/{topping}', [ToppingsController::class, 'destroy']);
