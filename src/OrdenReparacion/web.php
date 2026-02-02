<?php

use Illuminate\Support\Facades\Route;
use Src\OrdenReparacion\Application\Controllers\OrdenReparacionWebController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('ordenes-reparacion', OrdenReparacionWebController::class);
    Route::post('ordenes-reparacion/{id}/cambiar-estado', [OrdenReparacionWebController::class, 'cambiarEstado'])->name('ordenes-reparacion.cambiar-estado');
    Route::post('ordenes-reparacion/{id}/asignar-repuesto', [OrdenReparacionWebController::class, 'asignarRepuesto'])->name('ordenes-reparacion.asignar-repuesto');
    Route::delete('ordenes-reparacion/{ordenId}/repuesto/{repuestoId}', [OrdenReparacionWebController::class, 'quitarRepuesto'])->name('ordenes-reparacion.quitar-repuesto');
    Route::get('clientes/{clienteId}/equipos', [OrdenReparacionWebController::class, 'getEquiposPorCliente'])->name('clientes.equipos');
});
