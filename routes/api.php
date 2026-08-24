<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\WorkController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SettingController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

Route::get('/works', [WorkController::class, 'index']);
Route::get('/works/{id}', [WorkController::class, 'show']);

Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{id}', [PostController::class, 'show']);

Route::get('/settings', [SettingController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/me', [AuthController::class, 'update']);
    Route::get('/employees', [AuthController::class, 'employees']);
    Route::post('/employees', [AuthController::class, 'storeEmployee']);
    Route::post('/users/{user}/block', [AuthController::class, 'block']);
    Route::post('/users/{user}/unblock', [AuthController::class, 'unblock']);

    Route::post('/works', [WorkController::class, 'store']);
    Route::put('/works/{id}', [WorkController::class, 'update']);
    Route::delete('/works/{id}', [WorkController::class, 'destroy']);

    Route::post('/posts', [PostController::class, 'store']);
    Route::put('/posts/{id}', [PostController::class, 'update']);
    Route::delete('/posts/{id}', [PostController::class, 'destroy']);

    Route::put('/settings', [SettingController::class, 'update']);
});
