<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StoreController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\NotificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Kulaan.id
|--------------------------------------------------------------------------
| Prefix otomatis: /api/...
| Middleware 'auth:sanctum' melindungi endpoint yang butuh login.
| */

// ── Auth (publik) ────────────────────────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']); // US-01, US-08
    Route::post('login',    [AuthController::class, 'login']);    // US-02, US-09

    // Endpoint berikut butuh token
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me',      [AuthController::class, 'me']);
    });
});

// ── Produk (publik) ───────────────────────────────────────────────────────
Route::get('products', [ProductController::class, 'index']); // US-03, US-04
Route::get('products/{id}', [ProductController::class, 'show']); // US-05

// ── Protected Routes ──────────────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {
    // Toko / Store Setup & Profile
    Route::post('store/setup', [StoreController::class, 'setup']); // onboarding
    Route::post('store/profile', [StoreController::class, 'updateProfile']); // US-11

    // Pesanan / Orders
    Route::post('orders', [OrderController::class, 'store']); // US-06, US-07
    Route::get('orders/buyer', [OrderController::class, 'buyerOrders']); // US-06, US-07 list
    Route::get('orders/seller', [OrderController::class, 'sellerOrders']); // US-12, US-10 contact
    Route::put('orders/{id}/status', [OrderController::class, 'updateStatus']); // US-12

    // Notifikasi / Notifications
    Route::get('notifications', [NotificationController::class, 'index']); // US-07 list
    Route::put('notifications/{id}/read', [NotificationController::class, 'markAsRead']); // US-07 mark as read

    // Admin Routes
    Route::get('admin/dashboard', [AdminController::class, 'getDashboard']);
    Route::post('admin/stores/{id_store}/verify', [AdminController::class, 'verifyStore']);
});