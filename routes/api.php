<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PriceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\SubcategoryController;
use App\Http\Controllers\UserAddressController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

// Rutas públicas - Solo lectura del catálogo
Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);
Route::apiResource('subcategories', SubcategoryController::class)->only(['index', 'show']);
Route::apiResource('products', ProductController::class)->only(['index', 'show']);
Route::apiResource('prices', PriceController::class)->only(['index', 'show']);
Route::apiResource('discounts', DiscountController::class)->only(['index', 'show']);

// Rutas protegidas por login
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    
    // Operaciones de escritura del catálogo
    Route::apiResource('categories', CategoryController::class)->except(['index', 'show']);
    Route::apiResource('subcategories', SubcategoryController::class)->except(['index', 'show']);
    Route::apiResource('products', ProductController::class)->except(['index', 'show']);
    Route::apiResource('prices', PriceController::class)->except(['index', 'show']);
    Route::apiResource('discounts', DiscountController::class)->except(['index', 'show']);
    
    // Recursos completamente protegidos
    Route::apiResource('orders', OrderController::class);
    Route::apiResource('userAddresses', UserAddressController::class);
    Route::apiResource('users', UserController::class);
    Route::apiResource('roles', RolController::class);
});