<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Obtiene todas las métricas necesarias para el dashboard del usuario autenticado.
     *
     * @return array<string, mixed>
     */
    public function getDashboardData(): array
    {
        $user = Auth::user();
        if (! $user) {
            return $this->getEmptyData();
        }

        $now = Carbon::now();
        $currentMonthStart = $now->copy()->startOfMonth();
        $currentMonthEnd = $now->copy()->endOfMonth();
        $previousMonthStart = $now->copy()->subMonth()->startOfMonth();
        $previousMonthEnd = $now->copy()->endOfMonth();

        // 1. Gasto Total Mes Actual - usando where explícito en lugar de whereBelongsTo
        $currentMonthTotal = Expense::where('user_id', $user->id)
            ->whereBetween('date', [$currentMonthStart, $currentMonthEnd])
            ->sum('amount') ?? 0;

        // 2. Gasto Total Mes Anterior
        $previousMonthTotal = Expense::where('user_id', $user->id)
            ->whereBetween('date', [$previousMonthStart, $previousMonthEnd])
            ->sum('amount') ?? 0;

        // Cálculo de porcentaje de cambio
        $percentageChange = 0;
        if ($previousMonthTotal > 0) {
            $percentageChange = (($currentMonthTotal - $previousMonthTotal) / $previousMonthTotal) * 100;
        } elseif ($currentMonthTotal > 0) {
            $percentageChange = 100; // De 0 a algo positivo es 100% aumento
        }

        // 3. Gastos por Categoría (Para Gráfico de Dona)
        $expensesByCategory = Expense::where('user_id', $user->id)
            ->whereBetween('date', [$currentMonthStart, $currentMonthEnd])
            ->join('categories', 'expenses.category_id', '=', 'categories.id')
            ->select('categories.name', 'categories.color', 'categories.icon', DB::raw('SUM(expenses.amount) as total'))
            ->groupBy('categories.id', 'categories.name', 'categories.color', 'categories.icon')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($item) => [
                'name' => $item->name,
                'color' => $item->color,
                'icon' => $item->icon,
                'total' => round((float) $item->total, 2),
            ]);

        // 4. Evolución Diaria (Para Gráfico de Líneas - últimos 30 días o mes actual)
        // Usamos el mes actual día a día
        $dailyExpenses = Expense::where('user_id', $user->id)
            ->whereBetween('date', [$currentMonthStart, $currentMonthEnd])
            ->select(DB::raw('DATE(date) as date'), DB::raw('SUM(amount) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('total', 'date');

        // Rellenar días faltantes con 0 para que el gráfico sea continuo
        $chartLabels = [];
        $chartData = [];

        for ($day = 1; $day <= $now->daysInMonth; $day++) {
            $dateStr = $now->copy()->day($day)->format('Y-m-d');
            $chartLabels[] = $day; // Solo el número del día para el eje X

            $chartData[] = isset($dailyExpenses[$dateStr]) ? (float) $dailyExpenses[$dateStr] : 0;
        }

        return [
            'current_month_total' => round((float) $currentMonthTotal, 2),
            'previous_month_total' => round((float) $previousMonthTotal, 2),
            'percentage_change' => round((float) $percentageChange, 2),
            'expenses_by_category' => $expensesByCategory,
            'daily_chart_labels' => $chartLabels,
            'daily_chart_data' => $chartData,
        ];
    }

    /**
     * Retorna datos vacíos si no hay usuario o datos.
     */
    private function getEmptyData(): array
    {
        return [
            'current_month_total' => 0,
            'previous_month_total' => 0,
            'percentage_change' => 0,
            'expenses_by_category' => collect(),
            'daily_chart_labels' => [],
            'daily_chart_data' => [],
        ];
    }
}