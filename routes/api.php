<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\SettingController;

Route::prefix('v1')->group(function () {
    // Public routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/menus', [MenuController::class, 'index']);
    Route::get('/menus/category/{slug}', [MenuController::class, 'byCategory']);
    Route::get('/menus/{id}', [MenuController::class, 'show']);
    Route::get('/banners', [BannerController::class, 'index']);
    Route::get('/settings/bank-info', [SettingController::class, 'bankInfo']);
    Route::get('/settings/loyalty', [SettingController::class, 'loyaltyInfo']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/profile', [AuthController::class, 'profile']);
        
        Route::prefix('auth')->group(function () {
            Route::put('/profile', [AuthController::class, 'updateProfile']);
            Route::put('/change-password', [AuthController::class, 'changePassword']);
            Route::delete('/account', [AuthController::class, 'deleteAccount']);
        });
        
        Route::delete('/cart/clear', [CartController::class, 'clear']);
        Route::apiResource('/cart', CartController::class);
        Route::apiResource('/orders', OrderController::class);
        Route::apiResource('/categories', CategoryController::class)->except(['index', 'show']);
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

        // Admin Settings
        Route::prefix('admin')->group(function () {
            Route::get('/settings', [SettingController::class, 'index']);
            Route::put('/settings/{key}', [SettingController::class, 'update']);
            Route::post('/settings/reset-points', [SettingController::class, 'resetPoints']);
            
            // Member Management
            Route::get('/members', [MemberController::class, 'index']);
            Route::get('/members/{id}', [MemberController::class, 'show']);
            Route::put('/members/{id}/status', [MemberController::class, 'toggleStatus']);
            Route::post('/members/{id}/reset-points', [MemberController::class, 'resetPoints']);
        });
    });
});

