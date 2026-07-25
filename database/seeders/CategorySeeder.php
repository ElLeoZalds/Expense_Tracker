<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

/**
 * Seeder para crear las categorías por defecto del sistema
 */
class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categorias = [
            ['name' => 'Comida', 'icon' => '🍽️', 'color' => '#FF6B6B'],
            ['name' => 'Transporte', 'icon' => '🚗', 'color' => '#4ECDC4'],
            ['name' => 'Entretenimiento', 'icon' => '🎬', 'color' => '#45B7D1'],
            ['name' => 'Salud', 'icon' => '💊', 'color' => '#FFA07A'],
            ['name' => 'Educación', 'icon' => '📚', 'color' => '#98D8C8'],
            ['name' => 'Hogar', 'icon' => '🏠', 'color' => '#F7DC6F'],
            ['name' => 'Ropa', 'icon' => '👕', 'color' => '#BB8FCE'],
            ['name' => 'Otros', 'icon' => '📦', 'color' => '#95A5A6'],
        ];

        foreach ($categorias as $categoria) {
            Category::firstOrCreate(
                ['name' => $categoria['name']],
                [
                    'icon' => $categoria['icon'],
                    'color' => $categoria['color'],
                    'user_id' => 1,
                ]
            );
        }
    }
}
