<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\StoreController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\OrderController;

/*
|--------------------------------------------------------------------------
| API Routes — Kulaan.id
|--------------------------------------------------------------------------
| Prefix otomatis: /api/...
| Middleware 'auth:sanctum' melindungi endpoint yang butuh login.
*/

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

Route::middleware('auth:sanctum')->group(function () {
    Route::post('store/setup', [StoreController::class, 'setup']); // onboarding
    Route::put('store/profile', [StoreController::class, 'update']); // US-11

    // Product routes (US-03, US-05)
    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/{id}', [ProductController::class, 'show']);

    // Order routes (US-06, US-12)
    Route::post('orders', [OrderController::class, 'store']);
    Route::get('seller/orders', [OrderController::class, 'sellerIndex']);
    Route::patch('seller/orders/{id}/status', [OrderController::class, 'updateStatus']);
    
    // Admin Routes
    Route::get('admin/dashboard', [AdminController::class, 'getDashboard']);
    Route::post('admin/stores/{id_store}/verify', [AdminController::class, 'verifyStore']);
});

// ── Produk (diisi nanti P7 — US-03 Search Products) ───────────────────────
// Route::get('products', [ProductController::class, 'index']);
// Route::get('products/{id}', [ProductController::class, 'show']);