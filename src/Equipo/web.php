<?php

use Illuminate\Support\Facades\Route;
use Src\Equipo\Application\Controllers\EquipoWebController;

Route::middleware('auth')->group(function () {
    Route::resource('equipos', EquipoWebController::class);
});
