<?php

use App\Http\Controllers\PriceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RolController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::post('login', [AuthController::class, 'login']);
Route::post('register',[AuthController::class,'register']);

// Rutas protegidas por login
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('products', ProductController::class);
    Route::apiResource('roles',RolController::class);
    Route::apiResource('precios',PriceController::class);

    // Endpoint para logout
    Route::post('logout', [AuthController::class, 'logout']);
});
