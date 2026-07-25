<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Budget;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class BudgetSeeder extends Seeder
{
    /**
     * Ejecutar el seeder.
     */
    public function run(): void
    {
        // Crear presupuestos para usuarios existentes
        User::all()->each(function (User $user): void {
            // Obtener o crear categorías del usuario
            $categories = Category::where('user_id', $user->id)->get();

            if ($categories->isEmpty()) {
                return;
            }

            // Crear un presupuesto general para el mes actual
            Budget::factory()
                ->forUser($user)
                ->currentMonth()
                ->create([
                    'category_id' => null, // Presupuesto general
                    'amount_limit' => 2000.00,
                ]);

            // Crear presupuestos por categoría para el mes actual
            foreach ($categories as $category) {
                Budget::factory()
                    ->forUser($user)
                    ->forCategory($category)
                    ->currentMonth()
                    ->create([
                        'amount_limit' => fake()->randomFloat(2, 100, 500),
                    ]);
            }
        });
    }
}
