<?php

declare(strict_types=1);

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

/**
 * Global Scope para filtrar automáticamente las consultas por el usuario autenticado.
 */
class UserScope implements Scope
{
    /**
     * Aplica el scope a una consulta de Eloquent.
     *
     * @param  Builder<Model>  $builder
     */
    #[Override]
    public function apply(Builder $builder, Model $model): void
    {
        if (Auth::check()) {
            $builder->where($model->getTable().'.user_id', Auth::id());
        }
    }
}
