<?php

declare(strict_types=1);

use App\Http\Controllers\CategoryController;
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
    Route::resource('expenses', ExpenseController::class);
    Route::resource('categories', CategoryController::class);
});

// Ruta de downloads con verificación de ownership incluida en el closure
Route::get('/downloads/{filename}', function (Request $request, string $filename) {
    $user = Auth::user();

    if (! $user) {
        abort(401, 'Debes iniciar sesión para descargar archivos.');
    }

    // Buscar el registro de exportación que pertenece al usuario
    $fileExport = FileExport::where('user_id', $user->id)
        ->where('filename', $filename)
        ->first();

    if (! $fileExport) {
        abort(403, 'No tienes permiso para descargar este archivo.');
    }

    $path = $fileExport->path;
    $disk = $fileExport->disk ?? 'local';

    if (! Storage::disk($disk)->exists($path)) {
        abort(404, 'El archivo no existe.');
    }

    return Storage::disk($disk)->download($path, $fileExport->original_filename ?? $filename);
})->middleware(['auth', 'throttle:20,1'])->name('downloads.expenses');

require __DIR__.'/auth.php';
