<?php
// routes/api.php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SizeController;
use App\Http\Controllers\Api\DashboardController;

/*
|--------------------------------------------------------------------------
| API Routes v1
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    
    /*
    |--------------------------------------------------------------------------
    | DASHBOARD APIs
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
    
    /*
    |--------------------------------------------------------------------------
    | CATEGORY APIs
    |--------------------------------------------------------------------------
    */
    Route::prefix('categories')->group(function () {
        Route::post('/create', [CategoryController::class, 'store']);
        Route::get('/list', [CategoryController::class, 'index']);
        Route::get('/all-list', [CategoryController::class, 'list']);  // For dropdowns
        Route::get('/view/{id}', [CategoryController::class, 'show']);
        Route::post('/update/{id}', [CategoryController::class, 'update']);
        Route::post('/delete/{id}', [CategoryController::class, 'destroy']);
        Route::get('/{id}/products', [CategoryController::class, 'categoryProducts']);
    });
    
    /*
    |--------------------------------------------------------------------------
    | PRODUCT APIs
    |--------------------------------------------------------------------------
    */
    Route::prefix('products')->group(function () {
        // Basic CRUD
        Route::post('/create', [ProductController::class, 'store']);
        Route::get('/list', [ProductController::class, 'index']);
        Route::get('/view/{id}', [ProductController::class, 'show']);
        Route::post('/update/{id}', [ProductController::class, 'update']);
        Route::post('/delete/{id}', [ProductController::class, 'destroy']);
        
        // Additional Features
        Route::post('/restore/{id}', [ProductController::class, 'restore']);
        Route::post('/bulk-delete', [ProductController::class, 'bulkDelete']);
        Route::post('/bulk-update-stock', [ProductController::class, 'bulkUpdateStock']);
        Route::get('/search', [ProductController::class, 'search']);
        Route::post('/reduce-stock/{id}', [ProductController::class, 'reduceQuantity']);
    });
    
    /*
    |--------------------------------------------------------------------------
    | SIZE APIs
    |--------------------------------------------------------------------------
    */
    Route::prefix('sizes')->group(function () {
        Route::get('/list', [SizeController::class, 'index']);
        Route::get('/all-list', [SizeController::class, 'list']);
        Route::post('/create', [SizeController::class, 'store']);
        Route::get('/view/{id}', [SizeController::class, 'show']);
        Route::post('/update/{id}', [SizeController::class, 'update']);
        Route::post('/delete/{id}', [SizeController::class, 'destroy']);
    });
    
    /*
    |--------------------------------------------------------------------------
    | MASTER → CHILD (Legacy Support)
    |--------------------------------------------------------------------------
    */
    Route::get('/categoriesWiseProducts/{id}', [CategoryController::class, 'categoryProducts']);
});