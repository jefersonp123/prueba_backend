<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CurrencyController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductPriceController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('jwt')->group(function () {
    Route::get('products', [ProductController::class, 'index']);
    Route::post('products', [ProductController::class, 'store']);
    Route::get('products/{product}', [ProductController::class, 'show']);
    Route::put('products/{product}', [ProductController::class, 'update']);
    Route::delete('products/{product}', [ProductController::class, 'destroy']);
    Route::get('products/{product}/prices', [ProductPriceController::class, 'index']);
    Route::post('products/{product}/prices', [ProductPriceController::class, 'store']);
    /**
     * Rutas para carga de tasas de cambio segun moneda listar y crear
     */
    Route::get('currency', [CurrencyController::class, 'index']);
    Route::post('currency', [CurrencyController::class, 'store']);
    Route::get('currency/{currency}', [CurrencyController::class, 'show']);
});
