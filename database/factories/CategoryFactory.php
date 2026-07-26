<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'icon' => fake()->randomElement(['💰', '🛒', '🏠', '🚗', '🍔', '💡', '🎬', '🏥']),
            'color' => fake()->safeHexColor(),
            'user_id' => User::factory(),
        ];
    }

    /**
     * Indicate that the category is uncategorized.
     */
    public function uncategorized(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Uncategorized',
            'icon' => '❓',
            'color' => '#808080',
        ]);
    }
}
