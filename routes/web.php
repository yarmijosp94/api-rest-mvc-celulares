<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use Src\Repuesto\Application\Controllers\RepuestoWebController;

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('repuestos', RepuestoWebController::class);
});
