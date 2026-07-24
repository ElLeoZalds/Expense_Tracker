<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Expense>
 */
class ExpenseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $descripciones = [
            'Compra en supermercado',
            'Pago de gasolina',
            'Cena en restaurante',
            'Suscripción Netflix',
            'Consulta médica',
            'Libros de texto',
            'Pago de alquiler',
            'Ropa nueva',
            'Regalo cumpleaños',
            'Transporte público',
        ];

        return [
            'description' => fake()->randomElement($descripciones),
            'amount' => fake()->randomFloat(2, 5, 500),
            'date' => fake()->dateTimeBetween('-1 year', 'now'),
            'category_id' => Category::inRandomOrder()->first()?->id ?? Category::factory(),
            'user_id' => User::factory(),
            'notes' => fake()->optional(0.3)->sentence(10),
        ];
    }
}