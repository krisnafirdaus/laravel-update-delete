<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Auth\AuthenticationException;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/products', [ProductController::class, 'index']);
});

Route::get('/products/{id}', [ProductController::class, 'show']);
Route::post('/products', [ProductController::class, 'store']);
Route::put('/products/{product}', [ProductController::class, 'update']);
Route::patch('/products/{product}', [ProductController::class, 'update']);
Route::delete('/products/{product}', [ProductController::class, 'destroy']);

// soft delete
Route::get('/products-trash', [ProductController::class, 'trash']);
Route::post('/products/{id}/restore', [ProductController::class, 'restore']);
Route::delete('/products/{id}/force', [ProductController::class, 'forceDelete']);

Route::prefix('v1')->group(function () {
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('posts', PostController::class);
    Route::post('posts/{post}/publish', [PostController::class, 'publish']);
});




