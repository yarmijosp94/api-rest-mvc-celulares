<?php

use Illuminate\Support\Facades\Route;
use Src\Repuesto\Application\Controllers\RepuestoController;
use Src\Producto\Application\Controllers\ProductoController;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('repuestos', RepuestoController::class)->names([
        'index' => 'api.repuestos.index',
        'store' => 'api.repuestos.store',
        'show' => 'api.repuestos.show',
        'update' => 'api.repuestos.update',
        'destroy' => 'api.repuestos.destroy',
    ]);

    Route::apiResource('productos', ProductoController::class)->names([
        'index' => 'api.productos.index',
        'store' => 'api.productos.store',
        'show' => 'api.productos.show',
        'update' => 'api.productos.update',
        'destroy' => 'api.productos.destroy',
    ]);
});
