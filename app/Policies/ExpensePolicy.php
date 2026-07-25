<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Expense;
use App\Models\User;

/**
 * Policy para autorizar acciones sobre gastos.
 * Solo el dueño del gasto puede realizar acciones sobre él.
 */
class ExpensePolicy
{
    /**
     * Determina si el usuario puede ver cualquier gasto.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determina si el usuario puede ver un gasto específico.
     */
    public function view(User $user, Expense $expense): bool
    {
        return $user->id === $expense->user_id;
    }

    /**
     * Determina si el usuario puede crear un gasto.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determina si el usuario puede actualizar un gasto.
     */
    public function update(User $user, Expense $expense): bool
    {
        return $user->id === $expense->user_id;
    }

    /**
     * Determina si el usuario puede eliminar un gasto.
     */
    public function delete(User $user, Expense $expense): bool
    {
        return $user->id === $expense->user_id;
    }

    /**
     * Determina si el usuario puede restaurar un gasto eliminado.
     */
    public function restore(User $user, Expense $expense): bool
    {
        return $user->id === $expense->user_id;
    }

    /**
     * Determina si el usuario puede eliminar permanentemente un gasto.
     */
    public function forceDelete(User $user, Expense $expense): bool
    {
        return $user->id === $expense->user_id;
    }
}