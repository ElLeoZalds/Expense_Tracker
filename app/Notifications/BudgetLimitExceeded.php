<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Budget;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BudgetLimitExceeded extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * El porcentaje de límite alcanzado.
     */
    public int $percentage;

    /**
     * El presupuesto relacionado.
     */
    public Budget $budget;

    /**
     * Crear una nueva instancia de notificación.
     */
    public function __construct(Budget $budget, int $percentage)
    {
        $this->budget = $budget;
        $this->percentage = $percentage;
    }

    /**
     * Obtener los canales de entrega de la notificación.
     *
     * @param  mixed  $notifiable
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Obtener la representación en mail de la notificación.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $categoryName = $this->budget->category?->name ?? 'General';
        $monthName = now()->setMonth($this->budget->month)->format('F');

        return (new MailMessage)
            ->subject('⚠️ Alerta de Presupuesto Excedido')
            ->greeting('¡Hola!')
            ->line("Tu presupuesto para la categoría **{$categoryName}** ha superado el {$this->percentage}% del límite establecido.")
            ->line('Límite: $'.number_format((float) $this->budget->amount_limit, 2))
            ->line("Mes: {$monthName} {$this->budget->year}")
            ->action('Ver Presupuestos', route('budgets.index'))
            ->line('Te recomendamos revisar tus gastos para evitar sobrepasar tu presupuesto.');
    }

    /**
     * Obtener la representación en array de la notificación para base de datos.
     *
     * @param  mixed  $notifiable
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $categoryName = $this->budget->category?->name ?? 'General';

        return [
            'budget_id' => $this->budget->id,
            'category_id' => $this->budget->category_id,
            'category_name' => $categoryName,
            'amount_limit' => $this->budget->amount_limit,
            'percentage' => $this->percentage,
            'month' => $this->budget->month,
            'year' => $this->budget->year,
            'message' => "El presupuesto para '{$categoryName}' ha superado el {$this->percentage}% del límite.",
        ];
    }
}
