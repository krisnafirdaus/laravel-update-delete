<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Api\CategoryController;

Route::get('/products', [ProductController::class, 'index']);
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
});


