<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Budget extends Model
{
    /** @use HasFactory<\Database\Factories\BudgetFactory> */
    use HasFactory, SoftDeletes;

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'category_id',
        'amount_limit',
        'month',
        'year',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount_limit' => 'decimal:2',
            'month' => 'integer',
            'year' => 'integer',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Boot el modelo y aplicar el scope global de usuario.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new Scopes\UserScope());
    }

    /**
     * Obtener el usuario propietario del presupuesto.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtener la categoría asociada al presupuesto (puede ser null para presupuesto general).
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Scope para filtrar por mes y año específicos.
     *
     * @param \Illuminate\Database\Eloquent\Builder<$this> $query
     */
    public function scopeForMonth(
        \Illuminate\Database\Eloquent\Builder $query,
        int $month,
        int $year
    ): \Illuminate\Database\Eloquent\Builder {
        return $query->where('month', $month)->where('year', $year);
    }

    /**
     * Scope para filtrar por el mes actual.
     *
     * @param \Illuminate\Database\Eloquent\Builder<$this> $query
     */
    public function scopeCurrentMonth(
        \Illuminate\Database\Eloquent\Builder $query
    ): \Illuminate\Database\Eloquent\Builder {
        return $query->forMonth(now()->month, now()->year);
    }
}
