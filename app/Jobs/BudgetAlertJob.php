<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Budget;
use App\Models\Expense;
use App\Notifications\BudgetLimitExceeded;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class BudgetAlertJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * El presupuesto a verificar (puede ser null para verificar todos).
     */
    public ?Budget $budget;

    /**
     * Umbrales de alerta.
     */
    public const THRESHOLD_WARNING = 80;
    public const THRESHOLD_EXCEEDED = 100;

    /**
     * Crear una nueva instancia de Job.
     */
    public function __construct(?Budget $budget = null)
    {
        $this->budget = $budget;
    }

    /**
     * Ejecutar el Job.
     */
    public function handle(): void
    {
        if ($this->budget !== null) {
            $this->checkBudget($this->budget);
        } else {
            // Verificar todos los presupuestos del mes actual
            Budget::currentMonth()
                ->with(['user', 'category'])
                ->cursor()
                ->each(fn (Budget $budget) => $this->checkBudget($budget));
        }
    }

    /**
     * Verificar un presupuesto individual y enviar notificaciones si corresponde.
     */
    protected function checkBudget(Budget $budget): void
    {
        $month = $budget->month;
        $year = $budget->year;
        $userId = $budget->user_id;
        $categoryId = $budget->category_id;

        // Calcular el total gastado en la categoría (o todos los gastos si no hay categoría específica)
        $query = Expense::where('user_id', $userId)
            ->whereYear('date', $year)
            ->whereMonth('date', $month);

        if ($categoryId !== null) {
            $query->where('category_id', $categoryId);
        }

        $totalSpent = $query->sum('amount');

        if ($totalSpent <= 0 || $budget->amount_limit <= 0) {
            return;
        }

        $percentage = (int) round(($totalSpent / (float) $budget->amount_limit) * 100);

        // Determinar qué umbral se ha alcanzado
        $thresholdReached = null;

        if ($percentage >= self::THRESHOLD_EXCEEDED) {
            $thresholdReached = self::THRESHOLD_EXCEEDED;
        } elseif ($percentage >= self::THRESHOLD_WARNING) {
            $thresholdReached = self::THRESHOLD_WARNING;
        }

        if ($thresholdReached === null) {
            return;
        }

        // Verificar si ya se envió una notificación para este umbral
        $existingNotification = DB::table('notifications')
            ->where('notifiable_type', get_class($budget->user))
            ->where('notifiable_id', $userId)
            ->whereJsonContains('data->budget_id', $budget->id)
            ->whereJsonContains('data->percentage', $thresholdReached)
            ->where('created_at', '>=', now()->startOfMonth())
            ->first();

        if ($existingNotification !== null) {
            // Ya se envió una notificación para este umbral este mes
            return;
        }

        // Enviar notificación al usuario
        $budget->user->notify(new BudgetLimitExceeded($budget, $thresholdReached));
    }
}