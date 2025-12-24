<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;

/*
|--------------------------------------------------------------------------
| CATEGORY APIs (GET + POST ONLY)
|--------------------------------------------------------------------------
*/

// CREATE
Route::post('/categories/create', [CategoryController::class, 'store']);

// READ
Route::get('/categories/categoriesList', [CategoryController::class, 'index']);
Route::get('/categories/view/{id}', [CategoryController::class, 'show']);

// UPDATE (POST instead of PUT)
Route::post('/categories/update/{id}', [CategoryController::class, 'update']);

// DELETE (POST instead of DELETE)
Route::post('/categories/delete/{id}', [CategoryController::class, 'destroy']);

/*
|--------------------------------------------------------------------------
| PRODUCT APIs (GET + POST ONLY)
|--------------------------------------------------------------------------
*/

// CREATE
Route::post('/products/create', [ProductController::class, 'store']);

// READ
Route::get('/products/productsList', [ProductController::class, 'index']);
Route::get('/products/view/{id}', [ProductController::class, 'show']);

// UPDATE
Route::post('/products/update/{id}', [ProductController::class, 'update']);

// DELETE
Route::post('/products/delete/{id}', [ProductController::class, 'destroy']);


/*
|--------------------------------------------------------------------------
| MASTER → CHILD API
|--------------------------------------------------------------------------
| Get all products under a specific category
| GET method only
|--------------------------------------------------------------------------
*/

// Category products by ID
Route::get('/categoriesWiseProducts/{id}', [CategoryController::class, 'categoryProducts'])
     ->whereNumber('id');

