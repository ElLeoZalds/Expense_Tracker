<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Scopes\UserScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Modelo para las categorías de gastos
 */
class Category extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Los atributos que son asignables masivamente
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'icon',
        'color',
        'user_id',
    ];

    /**
     * Boot del modelo para aplicar el scope global.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new UserScope);
    }

    /**
     * Los atributos que deben ser casteado
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Relación: una categoría pertenece a un usuario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación: una categoría tiene muchos gastos
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    /**
     * Resolve route bindings without the user scope so policies can return 403s.
     */
    public function resolveRouteBinding($value, $field = null): ?Model
    {
        $field ??= $this->getRouteKeyName();

        return $this->withoutGlobalScopes()
            ->where($field, $value)
            ->first();
    }
}
