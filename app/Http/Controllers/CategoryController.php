<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controlador para gestionar las categorías de gastos.
 * Implementa CRUD completo con validación de relaciones.
 */
class CategoryController extends Controller
{
    /**
     * Lista todas las categorías del usuario con conteo de expenses.
     */
    public function index(): \Illuminate\Contracts\View\View
    {
        $userId = Auth::id() ?? 1;

        $categories = Category::withCount('expenses')
            ->where('user_id', $userId)
            ->orderBy('name')
            ->get();

        return view('categories.index', compact('categories'));
    }

    /**
     * Muestra el formulario para crear una nueva categoría.
     */
    public function create(): \Illuminate\Contracts\View\View
    {
        return view('categories.create');
    }

    /**
     * Guarda una nueva categoría en la base de datos.
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'icon' => 'nullable|string|max:10',
            'color' => 'nullable|string|max:7',
        ]);

        $userId = Auth::id() ?? 1;

        Category::create([
            'name' => $validated['name'],
            'icon' => $validated['icon'] ?? null,
            'color' => $validated['color'] ?? null,
            'user_id' => $userId,
        ]);

        return redirect()->route('categories.index')
            ->with('success', 'Categoría creada exitosamente.');
    }

    /**
     * Muestra los detalles de una categoría específica.
     */
    public function show(Category $category): \Illuminate\Contracts\View\View
    {
        $userId = Auth::id() ?? 1;

        if ($category->user_id !== $userId) {
            abort(403, 'No autorizado para ver esta categoría.');
        }

        return view('categories.show', compact('category'));
    }

    /**
     * Muestra el formulario para editar una categoría existente.
     */
    public function edit(Category $category): \Illuminate\Contracts\View\View
    {
        $userId = Auth::id() ?? 1;

        if ($category->user_id !== $userId) {
            abort(403, 'No autorizado para editar esta categoría.');
        }

        return view('categories.edit', compact('category'));
    }

    /**
     * Actualiza una categoría existente en la base de datos.
     */
    public function update(Request $request, Category $category): \Illuminate\Http\RedirectResponse
    {
        $userId = Auth::id() ?? 1;

        if ($category->user_id !== $userId) {
            abort(403, 'No autorizado para actualizar esta categoría.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'icon' => 'nullable|string|max:10',
            'color' => 'nullable|string|max:7',
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Categoría actualizada exitosamente.');
    }

    /**
     * Elimina una categoría (solo si no tiene expenses asociados).
     */
    public function destroy(Category $category): \Illuminate\Http\RedirectResponse
    {
        $userId = Auth::id() ?? 1;

        if ($category->user_id !== $userId) {
            abort(403, 'No autorizado para eliminar esta categoría.');
        }

        // Verificar si tiene expenses asociados
        if ($category->expenses()->count() > 0) {
            return redirect()->route('categories.index')
                ->with('error', 'No se puede eliminar la categoría porque tiene gastos asociados.');
        }

        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Categoría eliminada exitosamente.');
    }
}