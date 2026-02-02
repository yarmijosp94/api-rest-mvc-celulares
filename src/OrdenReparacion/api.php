<?php

use Illuminate\Support\Facades\Route;
use Src\OrdenReparacion\Application\Controllers\OrdenReparacionController;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('ordenes-reparacion', OrdenReparacionController::class);
    Route::post('ordenes-reparacion/{id}/cambiar-estado', [OrdenReparacionController::class, 'cambiarEstado']);
    Route::post('ordenes-reparacion/{id}/asignar-repuesto', [OrdenReparacionController::class, 'asignarRepuesto']);
    Route::delete('ordenes-reparacion/{ordenId}/repuesto/{repuestoId}', [OrdenReparacionController::class, 'quitarRepuesto']);
});
