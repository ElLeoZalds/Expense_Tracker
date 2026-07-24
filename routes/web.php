<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Rutas para el dashboard y recursos de ExpenseTracker
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth');
Route::resource('expenses', ExpenseController::class)->middleware('auth');
Route::resource('categories', CategoryController::class)->middleware('auth');