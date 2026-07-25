<?php

declare(strict_types=1);

namespace App\Models;

use App\Scopes\UserScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Modelo para los gastos del usuario
 */
class Expense extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Los atributos que son asignables masivamente
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'description',
        'amount',
        'date',
        'category_id',
        'user_id',
        'notes',
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
            'amount' => 'decimal:2',
            'date' => 'date',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Relación: un gasto pertenece a una categoría
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relación: un gasto pertenece a un usuario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
