<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SellerDashboardController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\StoreController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\NotificationController;

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

// ── Admin (tanpa auth agar bisa diakses dari src.test/admin) ───────────
Route::prefix('admin')->group(function () {
    Route::get('dashboard',                 [AdminController::class, 'getDashboard']);
    Route::post('stores/{id_store}/verify', [AdminController::class, 'verifyStore']);
});

Route::middleware('auth:sanctum')->group(function () {
    // ── Store onboarding (US-08 langkah 2) ──────────────────────────────
    Route::post('store/setup', [StoreController::class, 'setup']);

    // ── Buyer Orders ─────────────────────────────────────────────────────
    Route::get('orders',      [OrderController::class, 'index']);
    Route::get('orders/{id}', [OrderController::class, 'show']);
    Route::post('orders',     [OrderController::class, 'store']);

    // ── Notifications ───────────────────────────────────────────────────
    Route::get('notifications',             [NotificationController::class, 'index']);
    Route::post('notifications/mark-read',  [NotificationController::class, 'markAllRead']);
    Route::post('notifications/{id}/read',  [NotificationController::class, 'markAsRead']);

    // ── Seller ───────────────────────────────────────────────────────────
    Route::prefix('seller')->group(function () {
        // FR-12: Dashboard metrik + pesanan terbaru
        Route::get('dashboard',          [SellerDashboardController::class, 'index']);
        Route::get('orders/{id}',        [SellerDashboardController::class, 'show']);
        Route::put('orders/{id}/status', [SellerDashboardController::class, 'updateStatus']);

        // Kelola produk toko
        Route::get('products',        [ProductController::class, 'index']);
        Route::post('products',       [ProductController::class, 'store']);
        Route::match(['put', 'post'], 'products/{id}', [ProductController::class, 'update']);
        Route::delete('products/{id}',[ProductController::class, 'destroy']);

        // Profil toko
        Route::get('store',  [StoreController::class, 'show']);
        Route::match(['put', 'post'], 'store', [StoreController::class, 'update']);
    });
});

// ── Buyer / Public ──────────────────────────────────────────────────────
Route::get('products',     [ProductController::class, 'catalog']);
Route::get('products/{id}', [ProductController::class, 'show']);
Route::get('categories',   function () {
    return App\Models\Category::select('id_category', 'name_category')->get();
});

