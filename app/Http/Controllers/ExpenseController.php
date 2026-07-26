<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Expense;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * Controlador para gestionar los gastos (expenses) de la aplicación.
 * Implementa CRUD completo con autorización por usuario.
 */
class ExpenseController extends Controller
{
    /**
     * Lista todos los expenses del usuario autenticado con paginación.
     */
    public function index(): View
    {
        $user = Auth::user();

        if (! $user) {
            abort(401, 'Debes iniciar sesión para ver tus gastos.');
        }

        $expenses = Expense::with('category')
            ->orderBy('date', 'desc')
            ->paginate(10);

        return view('expenses.index', compact('expenses'));
    }

    /**
     * Muestra el formulario para crear un nuevo expense.
     */
    public function create(): View
    {
        $user = Auth::user();

        if (! $user) {
            abort(401, 'Debes iniciar sesión para crear gastos.');
        }

        $categories = Category::all();

        return view('expenses.create', compact('categories'));
    }

    /**
     * Guarda un nuevo expense en la base de datos.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if (! $user) {
            abort(401, 'Debes iniciar sesión para crear gastos.');
        }

        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')->where(fn ($q) => $q->where('user_id', $user->id)),
            ],
            'notes' => 'nullable|string',
        ]);

        Expense::create([
            'description' => $validated['description'],
            'amount' => $validated['amount'],
            'date' => $validated['date'],
            'category_id' => $validated['category_id'],
            'notes' => $validated['notes'] ?? null,
            'user_id' => $user->id,
        ]);

        return redirect()->route('expenses.index')
            ->with('success', 'Gasto creado exitosamente.');
    }

    /**
     * Muestra los detalles de un expense específico.
     */
    public function show(Expense $expense): View
    {
        $this->authorize('view', $expense);

        return view('expenses.show', compact('expense'));
    }

    /**
     * Muestra el formulario para editar un expense existente.
     */
    public function edit(Expense $expense): View
    {
        $this->authorize('update', $expense);

        $categories = Category::all();

        return view('expenses.edit', compact('expense', 'categories'));
    }

    /**
     * Actualiza un expense existente en la base de datos.
     */
    public function update(Request $request, Expense $expense): RedirectResponse
    {
        $this->authorize('update', $expense);

        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')->where(fn ($q) => $q->where('user_id', auth()->id())),
            ],
            'notes' => 'nullable|string',
        ]);

        // Asignación explícita para prevenir Mass Assignment
        $expense->update($request->only(['amount', 'description', 'category_id', 'date', 'notes']));

        return redirect()->route('expenses.index')
            ->with('success', 'Gasto actualizado exitosamente.');
    }

    /**
     * Elimina un expense de la base de datos.
     */
    public function destroy(Expense $expense): RedirectResponse
    {
        $this->authorize('delete', $expense);

        $expense->delete();

        return redirect()->route('expenses.index')
            ->with('success', 'Gasto eliminado exitosamente.');
    }
}