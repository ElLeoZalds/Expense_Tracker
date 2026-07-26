<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Expense;
use App\Services\DashboardService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

/**
 * Controlador para el dashboard con estadísticas financieras.
 */
class DashboardController extends Controller
{
    /**
     * Muestra el dashboard con estadísticas del usuario.
     * Inyecta DashboardService para obtener todas las métricas.
     */
    public function __construct(
        private readonly DashboardService $dashboardService
    ) {}

    /**
     * Muestra el dashboard con estadísticas del usuario.
     * - Total gastado en el mes actual vs mes anterior
     * - Gastos por categoría (para gráfico de dona)
     * - Evolución diaria (para gráfico de líneas)
     * - Últimos 5 expenses recientes
     */
    public function index(): View
    {
        // Verificar que el usuario esté autenticado
        if (! Auth::check()) {
            abort(401, 'Debes iniciar sesión para ver el dashboard.');
        }

        $user = Auth::user();

        // Obtener datos del dashboard mediante el servicio
        $dashboardData = $this->dashboardService->getDashboardData();

        // Extraer variables para la vista
        $totalMonth = $dashboardData['current_month_total'];
        $previousMonthTotal = $dashboardData['previous_month_total'];
        $percentageChange = $dashboardData['percentage_change'];
        $expensesByCategory = $dashboardData['expenses_by_category'];
        $dailyChartLabels = $dashboardData['daily_chart_labels'];
        $dailyChartData = $dashboardData['daily_chart_data'];

        // Formatear datos para gráficos
        $chartLabels = $expensesByCategory->pluck('name')->toArray();
        $chartData = $expensesByCategory->pluck('total')->toArray();
        $chartColors = $expensesByCategory->pluck('color')->toArray();
        $chartIcons = $expensesByCategory->pluck('icon')->toArray();

        // Últimos 5 gastos recientes - explícitamente filtrados por usuario autenticado
        $recentExpenses = Expense::where('user_id', $user->id)
            ->with('category')
            ->orderBy('date', 'desc')
            ->limit(5)
            ->get();

        // Contar total de categorías y gastos (explícitamente filtrados por usuario autenticado)
        $totalCategories = Category::where('user_id', $user->id)->count();
        $totalExpenses = Expense::where('user_id', $user->id)->count();

        return view('dashboard', compact(
            'totalMonth',
            'previousMonthTotal',
            'percentageChange',
            'totalCategories',
            'totalExpenses',
            'recentExpenses',
            'chartLabels',
            'chartData',
            'chartColors',
            'chartIcons',
            'dailyChartLabels',
            'dailyChartData'
        ));
    }
}