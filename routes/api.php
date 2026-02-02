<?php

use Illuminate\Support\Facades\Route;
use Src\Repuesto\Application\Controllers\RepuestoController;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('repuestos', RepuestoController::class)->names([
        'index' => 'api.repuestos.index',
        'store' => 'api.repuestos.store',
        'show' => 'api.repuestos.show',
        'update' => 'api.repuestos.update',
        'destroy' => 'api.repuestos.destroy',
    ]);
});
