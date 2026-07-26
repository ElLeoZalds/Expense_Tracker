<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserFinancialFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_complete_financial_flow_and_security_policies(): void
    {
        // 1. Registrar usuario
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
        ];
        $user = User::create($userData);

        // 2. Crear categoría
        $category = Category::create([
            'user_id' => $user->id,
            'name' => 'Comida',
            'icon' => 'utensils',
            'color' => '#FF5733',
        ]);

        $this->assertDatabaseHas('categories', ['name' => 'Comida', 'user_id' => $user->id]);

        // 3. Registrar 3 gastos
        Expense::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'description' => 'Almuerzo',
            'amount' => 15.50,
            'date' => now()->toDateString(),
        ]);

        Expense::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'description' => 'Cena',
            'amount' => 25.00,
            'date' => now()->toDateString(),
        ]);

        Expense::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'description' => 'Desayuno',
            'amount' => 8.75,
            'date' => now()->toDateString(),
        ]);

        // 4. Verificar Dashboard (Simulando autenticación)
        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
        // Asumiendo que el DashboardService pasa 'totalMonth' a la vista
        // Esto depende de cómo hayas implementado la Fase 2, pero verificamos que la vista cargue
        $response->assertViewIs('dashboard');

        // 5. Verificar Políticas de Seguridad (Otros usuarios no pueden acceder)
        $otherUser = User::create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => Hash::make('password123'),
        ]);

        // Recargar la relación para obtener los gastos creados
        $user->refresh();
        $expenseId = $user->expenses()->first()->id;

        // Intentar ver el gasto del primer usuario siendo el segundo usuario
        // Asumiendo una ruta tipo expenses.show
        // Si usas Resource Controller estándar:
        $responseOther = $this->actingAs($otherUser)->get(route('expenses.show', $expenseId));

        // Con GlobalScope activo, si el usuario no es el dueño, el modelo no se encuentra (404).
        // Esto es más seguro que un 403 porque no revela la existencia del registro.
        $responseOther->assertNotFound();

        // Intentar actualizar
        $responseUpdate = $this->actingAs($otherUser)->put(route('expenses.update', $expenseId), [
            'description' => 'Hacked',
            'amount' => 9999,
        ]);

        // Debería ser 404 (no encontrado) o 403 (prohibido), pero nunca 200
        // En este caso, al usar find() en el controller con GlobalScope, será 404
        $responseUpdate->assertNotFound();

        // Verificar que el dato NO cambió en la BD
        $this->assertDatabaseHas('expenses', [
            'id' => $expenseId,
            'description' => 'Almuerzo', // Debe seguir siendo el original
            'amount' => 15.50,
        ]);
    }
}
