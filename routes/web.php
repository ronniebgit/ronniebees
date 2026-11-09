<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\StorefrontController;
use Illuminate\Support\Facades\Route;

Route::get('/', [StorefrontController::class, 'index'])->name('home');
Route::get('/menu', [StorefrontController::class, 'menu'])->name('menu');
Route::get('/gift-card', [StorefrontController::class, 'giftCard'])->name('gift-card');
Route::post('/gift-card', [StorefrontController::class, 'giftCardLookup'])->name('gift-card.lookup');
Route::get('/product/{product}', [StorefrontController::class, 'show'])->name('product.show');

Route::get('/cart', [CartController::class, 'index'])->name('cart');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/{order}', [CheckoutController::class, 'confirmation'])->name('checkout.confirmation');
Route::get('/track', [StorefrontController::class, 'track'])->name('track');
Route::post('/track', [StorefrontController::class, 'trackLookup'])->name('track.lookup');

Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [AdminController::class, 'login'])->name('admin.login');
    Route::post('/admin/login', [AdminController::class, 'authenticate'])->name('admin.authenticate');
});

Route::middleware('auth')->group(function () {
    Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard.index');
    Route::get('/admin/orders', [AdminController::class, 'ordersIndex'])->name('admin.orders.index');
    Route::get('/admin/orders/{order}', [AdminController::class, 'showOrder'])->name('admin.orders.show');
    Route::post('/admin/orders/{order}/status', [AdminController::class, 'updateOrderStatus'])->name('admin.orders.status.update');
    Route::get('/admin/gift-cards', [AdminController::class, 'giftCardsIndex'])->name('admin.gift-cards.index');
    Route::post('/admin/gift-cards', [AdminController::class, 'giftCardStore'])->name('admin.gift-cards.store');
    Route::put('/admin/gift-cards/{giftCard}', [AdminController::class, 'giftCardUpdate'])->name('admin.gift-cards.update');
    Route::post('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');
});
