<?php

declare(strict_types=1);

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Models\FileExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

// Rutas protegidas con autenticación y rate limiting
// Nota: El middleware 'ownership' está disponible como respaldo, pero se recomienda
// usar $this->authorize() directamente en los controladores para mayor claridad
Route::middleware(['auth', 'throttle:60,1'])->group(function () {
    // Dashboard - Vista principal con estadísticas
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Operaciones de lectura - rate limit estándar (60/min)
    Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::get('/expenses/create', [ExpenseController::class, 'create'])->name('expenses.create');
    Route::get('/expenses/{expense}', [ExpenseController::class, 'show'])->name('expenses.show');
    Route::get('/expenses/{expense}/edit', [ExpenseController::class, 'edit'])->name('expenses.edit');
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');

    // Operaciones de creación/actualización - rate limit más estricto (20/min)
    Route::middleware('throttle:sensitive')->group(function () {
        Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
        Route::put('/expenses/{expense}', [ExpenseController::class, 'update'])->name('expenses.update');
        Route::patch('/expenses/{expense}', [ExpenseController::class, 'update']);
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::patch('/categories/{category}', [CategoryController::class, 'update']);
    });

    // Operaciones de eliminación - rate limit muy estricto (10/min)
    Route::middleware('throttle:delete-operations')->group(function () {
        Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    });
});

// Ruta de downloads con verificación de ownership incluida en el closure
Route::get('/downloads/{filename}', function (Request $request, string $filename) {
    $user = Auth::user();

    if (! $user) {
        return response()->json([
            'error' => 'No autorizado',
            'message' => 'Debes iniciar sesión para descargar archivos.',
        ], 401);
    }

    // Buscar el registro de exportación que pertenece al usuario
    $fileExport = FileExport::where('user_id', $user->id)
        ->where('filename', $filename)
        ->first();

    if (! $fileExport) {
        return response('No tienes permiso para descargar este archivo.', 403);
    }

    $path = $fileExport->path;
    $disk = $fileExport->disk ?? 'local';

    if (! Storage::disk($disk)->exists($path)) {
        abort(404, 'El archivo no existe.');
    }

    return Storage::disk($disk)->download($path, $fileExport->original_filename ?? $filename);
})->middleware(['throttle:20,1'])->name('downloads.expenses');

require __DIR__.'/auth.php';