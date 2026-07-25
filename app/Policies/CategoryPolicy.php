<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

/**
 * Policy para autorizar acciones sobre categorías.
 * Solo el dueño de la categoría puede realizar acciones sobre ella.
 */
class CategoryPolicy
{
    /**
     * Determina si el usuario puede ver cualquier categoría.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determina si el usuario puede ver una categoría específica.
     */
    public function view(User $user, Category $category): bool
    {
        return $user->id === $category->user_id;
    }

    /**
     * Determina si el usuario puede crear una categoría.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determina si el usuario puede actualizar una categoría.
     */
    public function update(User $user, Category $category): bool
    {
        return $user->id === $category->user_id;
    }

    /**
     * Determina si el usuario puede eliminar una categoría.
     */
    public function delete(User $user, Category $category): bool
    {
        return $user->id === $category->user_id;
    }

    /**
     * Determina si el usuario puede restaurar una categoría eliminada.
     */
    public function restore(User $user, Category $category): bool
    {
        return $user->id === $category->user_id;
    }

    /**
     * Determina si el usuario puede eliminar permanentemente una categoría.
     */
    public function forceDelete(User $user, Category $category): bool
    {
        return $user->id === $category->user_id;
    }
}