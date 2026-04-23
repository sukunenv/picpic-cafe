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
use App\Http\Controllers\Admin\MemberSearchController;
use App\Http\Controllers\PromotionController;

Route::prefix('v1')->group(function () {
    // Public routes
    // Public routes
    Route::prefix('auth')->group(function () {
        // Stricter throttle for password recovery to prevent spam
        Route::middleware('throttle:3,60')->group(function () {
            Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
            Route::post('/reset-password', [AuthController::class, 'resetPassword']);
        });

        // Throttle for login/register
        Route::middleware('throttle:10,1')->group(function () {
            Route::post('/register', [AuthController::class, 'register']);
            Route::post('/login', [AuthController::class, 'login']);
        });
    });
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/menus', [MenuController::class, 'index']);
    Route::get('/menus/featured', [MenuController::class, 'featured']);
    Route::get('/menus/category/{slug}', [MenuController::class, 'byCategory']);
    Route::get('/menus/{id}', [MenuController::class, 'show']);
    Route::get('/banners', [BannerController::class, 'index']);
    Route::get('/settings/bank-info', [SettingController::class, 'bankInfo']);
    Route::get('/settings/loyalty', [SettingController::class, 'loyaltyInfo']);

    // Protected routes (semua role yang login)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/profile', [AuthController::class, 'profile']);

        // Member-specific routes
        Route::prefix('auth')->group(function () {
            Route::put('/profile', [AuthController::class, 'updateProfile']);
            Route::put('/change-password', [AuthController::class, 'changePassword']);
            Route::delete('/account', [AuthController::class, 'deleteAccount']);
        });

        Route::delete('/cart/clear', [CartController::class, 'clear']);
        Route::apiResource('/cart', CartController::class)->except(['show']);

        // Orders: semua staff boleh buat order (kasir), tapi hanya via admin app
        Route::apiResource('/orders', OrderController::class)->except(['destroy']);

        // Staff-only routes (admin, owner, kasir)
        Route::middleware('role:admin,owner,kasir')->group(function () {
            Route::apiResource('/categories', CategoryController::class)->except(['index', 'show']);
            Route::apiResource('/menus', MenuController::class)->except(['index', 'show']);
            Route::patch('/menus/{id}/status', [MenuController::class, 'toggleStatus']);
            Route::apiResource('/banners', BannerController::class)->except(['index', 'show']);
        });

        // Admin & Owner only routes
        Route::middleware('role:admin,owner')->group(function () {
            // Analytics
            Route::prefix('analytics')->group(function () {
                Route::get('/dashboard-stats', [AnalyticsController::class, 'dashboardStats']);
                Route::get('/summary', [AnalyticsController::class, 'summary']);
                Route::get('/chart', [AnalyticsController::class, 'chart']);
                Route::get('/top-menus', [AnalyticsController::class, 'topMenus']);
                Route::get('/payment-methods', [AnalyticsController::class, 'paymentMethods']);
                Route::get('/peak-hours', [AnalyticsController::class, 'peakHours']);
            });

            // Admin Panel
            Route::prefix('admin')->group(function () {
                Route::get('/settings', [SettingController::class, 'index']);
                Route::put('/settings/{key}', [SettingController::class, 'update']);
                Route::post('/settings/reset-points', [SettingController::class, 'resetPoints']);

                // Member Management
                Route::get('/members', [MemberController::class, 'index']);
                Route::get('/members/search', [MemberSearchController::class, 'search']);
                Route::get('/members/{id}', [MemberController::class, 'show']);
                Route::put('/members/{id}/status', [MemberController::class, 'toggleStatus']);
                Route::post('/members/{id}/reset-points', [MemberController::class, 'resetPoints']);

                // Promotions
                Route::apiResource('/promotions', PromotionController::class);
            });
        });
    });
});

