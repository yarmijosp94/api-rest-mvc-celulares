<?php

use Illuminate\Support\Facades\Route;
use Src\OrdenReparacion\Application\Controllers\OrdenReparacionController;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('ordenes-reparacion', OrdenReparacionController::class)->names([
        'index' => 'api.ordenes-reparacion.index',
        'store' => 'api.ordenes-reparacion.store',
        'show' => 'api.ordenes-reparacion.show',
        'update' => 'api.ordenes-reparacion.update',
        'destroy' => 'api.ordenes-reparacion.destroy',
    ]);
    Route::post('ordenes-reparacion/{id}/cambiar-estado', [OrdenReparacionController::class, 'cambiarEstado'])->name('api.ordenes-reparacion.cambiar-estado');
    Route::post('ordenes-reparacion/{id}/asignar-repuesto', [OrdenReparacionController::class, 'asignarRepuesto'])->name('api.ordenes-reparacion.asignar-repuesto');
    Route::delete('ordenes-reparacion/{ordenId}/repuesto/{repuestoId}', [OrdenReparacionController::class, 'quitarRepuesto'])->name('api.ordenes-reparacion.quitar-repuesto');
});
