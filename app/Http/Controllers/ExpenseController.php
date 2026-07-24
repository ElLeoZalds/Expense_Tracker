<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controlador para gestionar los gastos (expenses) de la aplicación.
 * Implementa CRUD completo con autorización por usuario.
 */
class ExpenseController extends Controller
{
    /**
     * Lista todos los expenses del usuario autenticado con paginación.
     */
    public function index(): \Illuminate\Contracts\View\View
    {
        $userId = Auth::id() ?? 1; // Hardcodear a 1 si no hay auth

        $expenses = Expense::with('category')
            ->where('user_id', $userId)
            ->orderBy('date', 'desc')
            ->paginate(10);

        return view('expenses.index', compact('expenses'));
    }

    /**
     * Muestra el formulario para crear un nuevo expense.
     */
    public function create(): \Illuminate\Contracts\View\View
    {
        $userId = Auth::id() ?? 1;
        $categories = Category::where('user_id', $userId)->get();

        return view('expenses.create', compact('categories'));
    }

    /**
     * Guarda un nuevo expense en la base de datos.
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'category_id' => 'required|exists:categories,id',
            'notes' => 'nullable|string',
        ]);

        $userId = Auth::id() ?? 1;

        Expense::create([
            'description' => $validated['description'],
            'amount' => $validated['amount'],
            'date' => $validated['date'],
            'category_id' => $validated['category_id'],
            'notes' => $validated['notes'] ?? null,
            'user_id' => $userId,
        ]);

        return redirect()->route('expenses.index')
            ->with('success', 'Gasto creado exitosamente.');
    }

    /**
     * Muestra los detalles de un expense específico.
     */
    public function show(Expense $expense): \Illuminate\Contracts\View\View
    {
        $userId = Auth::id() ?? 1;

        // Verificar que el expense pertenece al usuario
        if ($expense->user_id !== $userId) {
            abort(403, 'No autorizado para ver este gasto.');
        }

        return view('expenses.show', compact('expense'));
    }

    /**
     * Muestra el formulario para editar un expense existente.
     */
    public function edit(Expense $expense): \Illuminate\Contracts\View\View
    {
        $userId = Auth::id() ?? 1;

        // Verificar ownership
        if ($expense->user_id !== $userId) {
            abort(403, 'No autorizado para editar este gasto.');
        }

        $categories = Category::where('user_id', $userId)->get();

        return view('expenses.edit', compact('expense', 'categories'));
    }

    /**
     * Actualiza un expense existente en la base de datos.
     */
    public function update(Request $request, Expense $expense): \Illuminate\Http\RedirectResponse
    {
        $userId = Auth::id() ?? 1;

        // Verificar ownership
        if ($expense->user_id !== $userId) {
            abort(403, 'No autorizado para actualizar este gasto.');
        }

        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'category_id' => 'required|exists:categories,id',
            'notes' => 'nullable|string',
        ]);

        $expense->update($validated);

        return redirect()->route('expenses.index')
            ->with('success', 'Gasto actualizado exitosamente.');
    }

    /**
     * Elimina un expense de la base de datos.
     */
    public function destroy(Expense $expense): \Illuminate\Http\RedirectResponse
    {
        $userId = Auth::id() ?? 1;

        // Verificar ownership
        if ($expense->user_id !== $userId) {
            abort(403, 'No autorizado para eliminar este gasto.');
        }

        $expense->delete();

        return redirect()->route('expenses.index')
            ->with('success', 'Gasto eliminado exitosamente.');
    }
}