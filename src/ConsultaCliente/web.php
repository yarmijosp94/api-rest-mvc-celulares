<?php

use Illuminate\Support\Facades\Route;
use Src\ConsultaCliente\Application\Controllers\ConsultaClienteController;

Route::prefix('consulta')->name('consulta.')->group(function () {
    Route::get('/', [ConsultaClienteController::class, 'index'])->name('index');
    Route::post('/buscar', [ConsultaClienteController::class, 'buscar'])->name('buscar');
    Route::post('/aprobar/{orden}', [ConsultaClienteController::class, 'aprobar'])->name('aprobar');
    Route::post('/rechazar/{orden}', [ConsultaClienteController::class, 'rechazar'])->name('rechazar');
});
