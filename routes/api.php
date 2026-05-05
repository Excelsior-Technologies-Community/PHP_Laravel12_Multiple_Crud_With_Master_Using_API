<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SizeController;
use Illuminate\Support\Facades\Route;

Route::post('/categories/create', [CategoryController::class, 'store']);
Route::get('/categories/categoriesList', [CategoryController::class, 'index']);
Route::get('/categories/view/{id}', [CategoryController::class, 'show']);
Route::post('/categories/update/{id}', [CategoryController::class, 'update']);
Route::post('/categories/delete/{id}', [CategoryController::class, 'destroy']);

Route::post('/sizes/create', [SizeController::class, 'store']);
Route::get('/sizes/sizesList', [SizeController::class, 'index']);
Route::get('/sizes/view/{id}', [SizeController::class, 'show']);
Route::post('/sizes/update/{id}', [SizeController::class, 'update']);
Route::post('/sizes/delete/{id}', [SizeController::class, 'destroy']);

Route::post('/products/create', [ProductController::class, 'store']);
Route::get('/products/productsList', [ProductController::class, 'index']);
Route::get('/products/view/{id}', [ProductController::class, 'show']);
Route::post('/products/update/{id}', [ProductController::class, 'update']);
Route::post('/products/delete/{id}', [ProductController::class, 'destroy']);
Route::get('/products/search', [ProductController::class, 'search']);

Route::get('/categoriesWiseProducts/{id}', [CategoryController::class, 'categoryProducts'])->whereNumber('id');