<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SellerDashboardController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\StoreController;
use App\Http\Controllers\Api\AdminController;

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

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me',      [AuthController::class, 'me']);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    // ── Store onboarding (US-08 langkah 2) ──────────────────────────────
    Route::post('store/setup', [StoreController::class, 'setup']);

    // ── Admin ────────────────────────────────────────────────────────────
    Route::prefix('admin')->group(function () {
        Route::get('dashboard',                 [AdminController::class, 'getDashboard']);
        Route::post('stores/{id_store}/verify', [AdminController::class, 'verifyStore']);
    });

    // ── Seller ───────────────────────────────────────────────────────────
    Route::prefix('seller')->group(function () {
        // FR-12: Dashboard metrik + pesanan terbaru
        Route::get('dashboard',   [SellerDashboardController::class, 'index']);
        Route::get('orders/{id}', [SellerDashboardController::class, 'show']);

        // Kelola produk toko
        Route::get('products',        [ProductController::class, 'index']);
        Route::post('products',       [ProductController::class, 'store']);
        Route::put('products/{id}',   [ProductController::class, 'update']);
        Route::delete('products/{id}',[ProductController::class, 'destroy']);
    });
});