<?php

use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\EcommerceController;
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

Route::prefix('catalog')->group(function () {
    Route::get('/', [CatalogController::class, 'index']);                        // listado general con filtros
    Route::get('/{id}', [CatalogController::class, 'show']);                     // detalle de producto
    Route::get('/category/{id}', [CatalogController::class, 'byCategory']);      // por categoría
    Route::get('/subcategory/{id}', [CatalogController::class, 'bySubcategory']); // por subcategoría
});

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

    // Rutas para que el usuario edite su carrito
    Route::get('/cart',                [EcommerceController::class, 'index']);          // Obtener carrito activo (o crearlo)
    Route::get('/cart/{cart}',         [EcommerceController::class, 'show']);           // Ver un carrito por ID (solo dueño)
    Route::put('/cart/{cart}',         [EcommerceController::class, 'update']);         // Bulk update del carrito + ítems
    Route::delete('/cart/{cart}',      [EcommerceController::class, 'destroy']);        // Vaciar carrito
    Route::post('/cart/{cart}/checkout',[EcommerceController::class, 'checkout']);      // Checkout (crea orden)

    Route::post('/cart/items',         [EcommerceController::class, 'store']);          // Agregar ítem al carrito activo
    Route::put('/cart/items/{item}',   [EcommerceController::class, 'updateItem']);     // Actualizar cantidad del ítem
    Route::delete('/cart/items/{item}',[EcommerceController::class, 'destroyItem']);    // Eliminar ítem del carrito
});