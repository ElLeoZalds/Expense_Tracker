<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Budget;
use App\Models\User;

class BudgetPolicy
{
    /**
     * Determinar si el usuario puede ver cualquier presupuesto.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determinar si el usuario puede ver un presupuesto específico.
     */
    public function view(User $user, Budget $budget): bool
    {
        return $user->id === $budget->user_id;
    }

    /**
     * Determinar si el usuario puede crear presupuestos.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determinar si el usuario puede actualizar un presupuesto.
     */
    public function update(User $user, Budget $budget): bool
    {
        return $user->id === $budget->user_id;
    }

    /**
     * Determinar si el usuario puede eliminar un presupuesto.
     */
    public function delete(User $user, Budget $budget): bool
    {
        return $user->id === $budget->user_id;
    }

    /**
     * Determinar si el usuario puede restaurar un presupuesto eliminado.
     */
    public function restore(User $user, Budget $budget): bool
    {
        return $user->id === $budget->user_id;
    }

    /**
     * Determinar si el usuario puede eliminar permanentemente un presupuesto.
     */
    public function forceDelete(User $user, Budget $budget): bool
    {
        return $user->id === $budget->user_id;
    }
}
