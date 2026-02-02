<?php

use Illuminate\Support\Facades\Route;
use Src\Tecnico\Application\Controllers\TecnicoWebController;

Route::middleware('auth')->group(function () {
    Route::resource('tecnicos', TecnicoWebController::class);
});
