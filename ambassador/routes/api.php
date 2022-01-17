<?php

use App\Http\Controllers\AmbassadorController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\OrdereController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StatsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

function commonRoutes($abilities)
{

    Route::post('users/register', [AuthController::class, 'register']);
    Route::post('users/login', [AuthController::class, 'login']);
    
    Route::middleware(['auth:sanctum', 'abilities:' . $abilities])->group(function(){
        Route::get('users/user', [AuthController::class, 'user']);
        Route::get('users/logout', [AuthController::class, 'logout']);
        Route::patch('users/update_profile', [AuthController::class, 'updateProfile']);
        Route::patch('users/update_password', [AuthController::class, 'updatePassword']);
    });
}

// Admin
Route::prefix('admin')->group(function(){
    commonRoutes('admin');
    Route::middleware(['auth:sanctum', 'abilities:admin'])->group(function(){
        Route::get('ambassadors', [AmbassadorController::class, 'index']);
        Route::apiResource('products', ProductController::class);
        Route::apiResource('links', LinkController::class);
        Route::get('links/{user_id}/all', [LinkController::class, 'all']);
        Route::get('users/{id}/links', [AuthController::class, 'showLinks']);
        Route::get('orders', [OrdereController::class, 'index']);
    });
});

// Ambassador
Route::prefix('ambassador')->group(function(){
    commonRoutes('ambassador');
    Route::get('products/frontend', [ProductController::class, 'index']);
    Route::get('products/backend', [ProductController::class, 'backend']);
    Route::middleware(['auth:sanctum', 'abilities:admin'])->group(function(){
        Route::get('stats', [StatsController::class, 'index']);
        Route::get('rankings', [StatsController::class, 'rankings']);
        Route::apiResource('links', LinkController::class)->only(['store']);
    });
});

// Checkout
Route::prefix('checkout')->group(function(){
    Route::apiResource('links', LinkController::class)->only(['show']);
    Route::post('orders', [OrdereController::class, 'store']);
    Route::post('orders/confirm', [OrdereController::class, 'confirm']);
});