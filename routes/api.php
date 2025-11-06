<?php

use App\Http\Controllers\PriceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RolController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

// Rutas públicas (GET/index/show)
Route::get('prices', [PriceController::class, 'index']);
Route::get('prices/{id}', [PriceController::class, 'show']);

Route::get('products', [ProductController::class, 'index']);
Route::get('products/{id}', [ProductController::class, 'show']);

// Rutas protegidas por login
Route::middleware('auth:sanctum')->group(function () {
    // Logout
    Route::post('logout', [AuthController::class, 'logout']);
    
    // Prices - solo operaciones de escritura
    Route::post('prices', [PriceController::class, 'store']);
    Route::put('prices/{id}', [PriceController::class, 'update']);
    Route::patch('prices/{id}', [PriceController::class, 'update']);
    Route::delete('prices/{id}', [PriceController::class, 'destroy']);
    
    // Products - solo operaciones de escritura
    Route::post('products', [ProductController::class, 'store']);
    Route::put('products/{id}', [ProductController::class, 'update']);
    Route::patch('products/{id}', [ProductController::class, 'update']);
    Route::delete('products/{id}', [ProductController::class, 'destroy']);
    
    // Roles - todo protegido
    Route::apiResource('roles', RolController::class);
});