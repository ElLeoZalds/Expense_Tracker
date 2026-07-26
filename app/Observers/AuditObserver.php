<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Category;
use App\Models\Expense;
use Illuminate\Support\Facades\Auth;

/**
 * Observer para registrar actividades de auditoría en modelos Expense y Category.
 * Registra creación, actualización y eliminación (soft delete) con detalles completos.
 */
class AuditObserver
{
    /**
     * Registrar evento de creación.
     */
    public function created(Expense|Category $model): void
    {
        $this->logActivity($model, 'created', null, $this->getModelData($model));
    }

    /**
     * Registrar evento de actualización.
     */
    public function updated(Expense|Category $model): void
    {
        $oldValues = $this->getModelData($model, $model->getOriginal());
        $newValues = $this->getModelData($model);

        // Solo registrar si hubo cambios reales
        if ($oldValues !== $newValues) {
            $this->logActivity($model, 'updated', $oldValues, $newValues);
        }
    }

    /**
     * Registrar evento de eliminación soft delete.
     */
    public function deleted(Expense|Category $model): void
    {
        $this->logActivity($model, 'deleted', $this->getModelData($model), null);
    }

    /**
     * Registrar evento de restauración después de soft delete.
     */
    public function restored(Expense|Category $model): void
    {
        $this->logActivity($model, 'restored', null, $this->getModelData($model));
    }

    /**
     * Registrar evento de eliminación permanente (force delete).
     */
    public function forceDeleted(Expense|Category $model): void
    {
        $this->logActivity($model, 'force_deleted', $this->getModelData($model), null);
    }

    /**
     * Obtener los datos relevantes del modelo para auditoría.
     */
    private function getModelData(Expense|Category $model, array $attributes = []): array
    {
        $data = $attributes ?: $model->toArray();

        // Filtrar campos sensibles que no deben auditarse
        unset($data['remember_token']);

        return [
            'id' => $model->id,
            'type' => get_class($model),
            'attributes' => $data,
        ];
    }

    /**
     * Registrar la actividad en la base de datos.
     */
    private function logActivity(Expense|Category $model, string $action, ?array $oldValues, ?array $newValues): void
    {
        \App\Models\AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}