<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Budget;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Budget>
 */
class BudgetFactory extends Factory
{
    /**
     * Define el estado por defecto del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'amount_limit' => fake()->randomFloat(2, 100, 5000),
            'month' => fake()->numberBetween(1, 12),
            'year' => now()->year,
        ];
    }

    /**
     * Indica que el presupuesto es para el mes actual.
     */
    public function currentMonth(): static
    {
        return $this->state(fn (array $attributes) => [
            'month' => now()->month,
            'year' => now()->year,
        ]);
    }

    /**
     * Indica que el presupuesto es para una categoría específica.
     */
    public function forCategory(Category $category): static
    {
        return $this->state(fn (array $attributes) => [
            'category_id' => $category->id,
        ]);
    }

    /**
     * Indica que el presupuesto es para un usuario específico.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }
}
