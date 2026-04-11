<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\AnalyticsController;

Route::prefix('v1')->group(function () {
    // Public routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/menus', [MenuController::class, 'index']);
    Route::get('/menus/category/{slug}', [MenuController::class, 'byCategory']);
    Route::get('/menus/{id}', [MenuController::class, 'show']);
    Route::get('/banners', [BannerController::class, 'index']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/profile', [AuthController::class, 'profile']);
        
        Route::apiResource('/cart', CartController::class);
        Route::apiResource('/orders', OrderController::class);
        Route::apiResource('/menus', MenuController::class)->except(['index', 'show']);
        Route::patch('/menus/{id}/status', [MenuController::class, 'toggleStatus']);
        Route::apiResource('/banners', BannerController::class)->except(['index', 'show']);

        // Analytics
        Route::prefix('analytics')->group(function () {
            Route::get('/summary', [AnalyticsController::class, 'summary']);
            Route::get('/chart', [AnalyticsController::class, 'chart']);
            Route::get('/top-menus', [AnalyticsController::class, 'topMenus']);
            Route::get('/payment-methods', [AnalyticsController::class, 'paymentMethods']);
            Route::get('/peak-hours', [AnalyticsController::class, 'peakHours']);
        });
    });
});

