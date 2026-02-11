<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use Src\Repuesto\Application\Controllers\RepuestoWebController;
use Src\Producto\Application\Controllers\ProductoWebController;

Route::get('/up', fn () => response()->json(['status' => 'ok']));

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('repuestos', RepuestoWebController::class);
    Route::resource('productos', ProductoWebController::class);
});
