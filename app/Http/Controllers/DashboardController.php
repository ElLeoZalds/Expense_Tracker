<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * Controlador para el dashboard con estadísticas financieras.
 */
class DashboardController extends Controller
{
    /**
     * Muestra el dashboard con estadísticas del usuario.
     * - Total gastado en el mes actual
     * - Total gastado en el año actual
     * - Gastos por categoría (para gráfico)
     * - Últimos 5 expenses recientes
     */
    public function index(): \Illuminate\Contracts\View\View
    {
        $userId = Auth::id() ?? 1;

        // Calcular fechas para consultas
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $startOfYear = Carbon::now()->startOfYear();
        $endOfYear = Carbon::now()->endOfYear();

        // Total gastado en el mes actual
        $totalMonth = Expense::where('user_id', $userId)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        // Total gastado en el año actual
        $totalYear = Expense::where('user_id', $userId)
            ->whereBetween('date', [$startOfYear, $endOfYear])
            ->sum('amount');

        // Gastos por categoría (para gráfico)
        $expensesByCategory = Expense::select('category_id', 'amount')
            ->where('user_id', $userId)
            ->whereBetween('date', [$startOfYear, $endOfYear])
            ->with('category')
            ->get()
            ->groupBy('category_id')
            ->map(function ($items) {
                return $items->sum('amount');
            });

        // Formatear datos para el gráfico
        $chartLabels = [];
        $chartData = [];
        foreach ($expensesByCategory as $categoryId => $total) {
            $category = Category::find($categoryId);
            if ($category) {
                $chartLabels[] = $category->name;
                $chartData[] = $total;
            }
        }

        // Últimos 5 expenses recientes
        $recentExpenses = Expense::with('category')
            ->where('user_id', $userId)
            ->orderBy('date', 'desc')
            ->limit(5)
            ->get();

        // Contar total de categorías y gastos
        $totalCategories = Category::where('user_id', $userId)->count();
        $totalExpenses = Expense::where('user_id', $userId)->count();

        return view('dashboard', compact(
            'totalMonth',
            'totalYear',
            'totalCategories',
            'totalExpenses',
            'recentExpenses',
            'chartLabels',
            'chartData'
        ));
    }
}