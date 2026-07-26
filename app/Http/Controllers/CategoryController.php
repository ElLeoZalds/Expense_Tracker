<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controlador para gestionar las categorías de gastos.
 * Implementa CRUD completo con autorización mediante Policies.
 */
class CategoryController extends Controller
{
    /**
     * Lista todas las categorías del usuario con conteo de expenses.
     */
    public function index(): View
    {
        $user = Auth::user();

        if (! $user) {
            abort(401, 'Debes iniciar sesión para ver tus categorías.');
        }

        $categories = Category::withCount('expenses')
            ->where('user_id', $user->id)
            ->orderBy('name')
            ->get();

        return view('categories.index', compact('categories'));
    }

    /**
     * Muestra el formulario para crear una nueva categoría.
     */
    public function create(): View
    {
        $this->authorize('create', Category::class);

        return view('categories.create');
    }

    /**
     * Guarda una nueva categoría en la base de datos.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Category::class);

        $user = Auth::user();

        if (! $user) {
            abort(401, 'Debes iniciar sesión para crear categorías.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'icon' => 'nullable|string|max:10',
            'color' => 'nullable|string|max:7',
        ]);

        Category::create([
            'name' => $validated['name'],
            'icon' => $validated['icon'] ?? null,
            'color' => $validated['color'] ?? null,
            'user_id' => $user->id,
        ]);

        return redirect()->route('categories.index')
            ->with('success', 'Categoría creada exitosamente.');
    }

    /**
     * Muestra los detalles de una categoría específica.
     */
    public function show(Category $category): View
    {
        $this->authorize('view', $category);

        return view('categories.show', compact('category'));
    }

    /**
     * Muestra el formulario para editar una categoría existente.
     */
    public function edit(Category $category): View
    {
        $this->authorize('update', $category);

        return view('categories.edit', compact('category'));
    }

    /**
     * Actualiza una categoría existente en la base de datos.
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        $this->authorize('update', $category);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,'.$category->id,
            'icon' => 'nullable|string|max:10',
            'color' => 'nullable|string|max:7',
        ]);

        // Asignación explícita para prevenir Mass Assignment
        $category->update($request->only(['name', 'icon', 'color']));

        return redirect()->route('categories.index')
            ->with('success', 'Categoría actualizada exitosamente.');
    }

    /**
     * Elimina una categoría (solo si no tiene expenses asociados).
     */
    public function destroy(Category $category): RedirectResponse
    {
        $this->authorize('delete', $category);

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
