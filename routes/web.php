<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

// Ruta pública
Route::get('/', function () {
    return view('welcome');
});

// Ruta del dashboard (usa TU controlador, no el closure de Breeze)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Rutas de perfil (de Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// TUS RUTAS DE NEGOCIO (agregar estas)
Route::middleware(['auth'])->group(function () {
    Route::resource('expenses', ExpenseController::class);
    Route::resource('categories', CategoryController::class);
});

require __DIR__.'/auth.php';