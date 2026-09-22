<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\Admin;
use App\Http\Controllers\Api\V1\User;

Route::prefix('v1')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);

        // User Routes
        Route::get('profile', [User\ProfileController::class, 'index']);
        Route::get('books', [User\BookController::class, 'index']);
        Route::get('orders', [User\OrderController::class, 'index']);
        Route::post('orders', [User\OrderController::class, 'store']);
        Route::get('chat', [User\ChatController::class, 'index']);
        Route::post('chat', [User\ChatController::class, 'store']);

        // Admin Routes
        Route::middleware('admin')->prefix('admin')->group(function () {
            Route::apiResource('categories', Admin\CategoryController::class);
            Route::apiResource('books', Admin\BookController::class);
            Route::apiResource('users', Admin\UserController::class);
            Route::apiResource('orders', Admin\OrderController::class);
            Route::get('reports', [Admin\ReportController::class, 'index']);
            Route::get('chat', [Admin\ChatController::class, 'index']);
            Route::post('chat', [Admin\ChatController::class, 'store']);
        });
    });
});