<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\DashboardService;

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
    ) {
    }

    /**
     * Muestra el dashboard con estadísticas del usuario.
     * - Total gastado en el mes actual vs mes anterior
     * - Gastos por categoría (para gráfico de dona)
     * - Evolución diaria (para gráfico de líneas)
     * - Últimos 5 expenses recientes
     */
    public function index(): \Illuminate\Contracts\View\View
    {
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

        // Últimos 5 gastos recientes
        $recentExpenses = \App\Models\Expense::with('category')
            ->orderBy('date', 'desc')
            ->limit(5)
            ->get();

        // Contar total de categorías y gastos (ya filtrados por UserScope)
        $totalCategories = \App\Models\Category::count();
        $totalExpenses = \App\Models\Expense::count();

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