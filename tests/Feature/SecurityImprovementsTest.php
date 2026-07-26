<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Expense;
use App\Models\FileExport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SecurityImprovementsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test 1.1: Verifica que un usuario NO puede descargar archivos de otro usuario (IDOR fix)
     */
    public function test_user_cannot_download_another_users_file(): void
    {
        // Crear dos usuarios
        $user1 = User::create([
            'name' => 'Usuario 1',
            'email' => 'user1@example.com',
            'password' => bcrypt('password123'),
        ]);

        $user2 = User::create([
            'name' => 'Usuario 2',
            'email' => 'user2@example.com',
            'password' => bcrypt('password123'),
        ]);

        // Crear un archivo exportado para el usuario 1
        Storage::disk('local')->put('private/export_user1.csv', 'contenido_archivo_1');
        $fileExport = FileExport::create([
            'user_id' => $user1->id,
            'filename' => 'export_user1.csv',
            'original_filename' => 'mis_gastos.csv',
            'disk' => 'local',
            'path' => 'private/export_user1.csv',
        ]);

        // Usuario 2 intenta descargar el archivo del usuario 1
        $response = $this->actingAs($user2)->get(route('downloads.expenses', ['filename' => 'export_user1.csv']));

        // Debe retornar 403 Forbidden
        $response->assertStatus(403);
        $response->assertContent('No tienes permiso para descargar este archivo.');
    }

    /**
     * Test 1.2: Verifica que un usuario SÍ puede descargar sus propios archivos
     */
    public function test_user_can_download_own_file(): void
    {
        $user = User::create([
            'name' => 'Usuario Propietario',
            'email' => 'owner@example.com',
            'password' => bcrypt('password123'),
        ]);

        Storage::disk('local')->put('private/my_export.csv', 'mi_contenido');
        $fileExport = FileExport::create([
            'user_id' => $user->id,
            'filename' => 'my_export.csv',
            'original_filename' => 'mis_datos.csv',
            'disk' => 'local',
            'path' => 'private/my_export.csv',
        ]);

        $response = $this->actingAs($user)->get(route('downloads.expenses', ['filename' => 'my_export.csv']));

        $response->assertStatus(200);
        $response->assertDownload('mis_datos.csv');
    }

    /**
     * Test 1.3: Verifica que usuario no autenticado no puede descargar archivos
     */
    public function test_unauthenticated_user_cannot_download_files(): void
    {
        $response = $this->get(route('downloads.expenses', ['filename' => 'any_file.csv']));

        $response->assertStatus(401);
    }

    /**
     * Test 1.4: Verifica que el middleware EnsureOwnership bloquea acceso a expenses de otros usuarios
     */
    public function test_ownership_middleware_blocks_access_to_other_users_expenses(): void
    {
        $user1 = User::create([
            'name' => 'Usuario 1',
            'email' => 'user1@test.com',
            'password' => bcrypt('password123'),
        ]);

        $user2 = User::create([
            'name' => 'Usuario 2',
            'email' => 'user2@test.com',
            'password' => bcrypt('password123'),
        ]);

        // Crear categoría y expense para user1
        $category = Category::create([
            'user_id' => $user1->id,
            'name' => 'Test Category',
            'icon' => 'test',
            'color' => '#FF0000',
        ]);

        $expense = Expense::create([
            'user_id' => $user1->id,
            'category_id' => $category->id,
            'description' => 'Expense of user 1',
            'amount' => 100.00,
            'date' => now()->toDateString(),
        ]);

        // User2 intenta ver el expense de user1
        $response = $this->actingAs($user2)->get(route('expenses.show', $expense->id));

        // Debe ser bloqueado con 403
        $response->assertStatus(403);
    }

    /**
     * Test 1.5: Verifica que el middleware EnsureOwnership permite acceso a recursos propios
     */
    public function test_ownership_middleware_allows_access_to_own_expenses(): void
    {
        $user = User::create([
            'name' => 'Usuario Dueño',
            'email' => 'owner@test.com',
            'password' => bcrypt('password123'),
        ]);

        $category = Category::create([
            'user_id' => $user->id,
            'name' => 'Mi Categoría',
            'icon' => 'test',
            'color' => '#00FF00',
        ]);

        $expense = Expense::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'description' => 'Mi gasto',
            'amount' => 50.00,
            'date' => now()->toDateString(),
        ]);

        $response = $this->actingAs($user)->get(route('expenses.show', $expense->id));

        $response->assertStatus(200);
    }

    /**
     * Test 1.6: Verifica que el middleware EnsureOwnership bloquea acceso a categorías de otros usuarios
     */
    public function test_ownership_middleware_blocks_access_to_other_users_categories(): void
    {
        $user1 = User::create([
            'name' => 'Usuario 1',
            'email' => 'user1@cat.com',
            'password' => bcrypt('password123'),
        ]);

        $user2 = User::create([
            'name' => 'Usuario 2',
            'email' => 'user2@cat.com',
            'password' => bcrypt('password123'),
        ]);

        $category = Category::create([
            'user_id' => $user1->id,
            'name' => 'Categoría Privada',
            'icon' => 'lock',
            'color' => '#0000FF',
        ]);

        // User2 intenta ver la categoría de user1
        $response = $this->actingAs($user2)->get(route('categories.show', $category->id));

        $response->assertStatus(403);
    }

    /**
     * Test 1.7: Verifica que se puede acceder a categorías propias
     */
    public function test_ownership_middleware_allows_access_to_own_categories(): void
    {
        $user = User::create([
            'name' => 'Usuario Cat Owner',
            'email' => 'catowner@test.com',
            'password' => bcrypt('password123'),
        ]);

        $category = Category::create([
            'user_id' => $user->id,
            'name' => 'Mi Categoría Test',
            'icon' => 'check',
            'color' => '#00FF00',
        ]);

        $response = $this->actingAs($user)->get(route('categories.show', $category->id));

        $response->assertStatus(200);
    }

    /**
     * Test 1.8: Verifica que archivo inexistente retorna 404 incluso si hay registro
     */
    public function test_missing_file_returns_404(): void
    {
        $user = User::create([
            'name' => 'Usuario Archivo',
            'email' => 'fileuser@test.com',
            'password' => bcrypt('password123'),
        ]);

        // Crear registro pero sin el archivo físico
        FileExport::create([
            'user_id' => $user->id,
            'filename' => 'nonexistent.csv',
            'original_filename' => 'no_existe.csv',
            'disk' => 'local',
            'path' => 'private/nonexistent.csv',
        ]);

        $response = $this->actingAs($user)->get(route('downloads.expenses', ['filename' => 'nonexistent.csv']));

        $response->assertStatus(404);
    }

    /**
     * Test 1.9: Verifica que un usuario NO puede crear un expense con categoría de otro usuario
     */
    public function test_user_cannot_create_expense_with_another_users_category(): void
    {
        $user1 = User::create([
            'name' => 'Usuario 1',
            'email' => 'user1@expense.com',
            'password' => bcrypt('password123'),
        ]);

        $user2 = User::create([
            'name' => 'Usuario 2',
            'email' => 'user2@expense.com',
            'password' => bcrypt('password123'),
        ]);

        // Crear categoría para user1
        $category1 = Category::create([
            'user_id' => $user1->id,
            'name' => 'Categoría Usuario 1',
            'icon' => 'test',
            'color' => '#FF0000',
        ]);

        // User2 intenta crear un expense usando la categoría de user1
        $response = $this->actingAs($user2)->post(route('expenses.store'), [
            'description' => 'Gasto malicioso',
            'amount' => 100.00,
            'date' => now()->toDateString(),
            'category_id' => $category1->id, // Intenta usar categoría de otro usuario
        ]);

        // La validación debe fallar porque la categoría no pertenece a user2
        $response->assertSessionHasErrors(['category_id']);
        
        // Verificar que no se creó ningún expense
        $this->assertDatabaseMissing('expenses', [
            'description' => 'Gasto malicioso',
            'category_id' => $category1->id,
        ]);
    }

    /**
     * Test 1.10: Verifica que un usuario SÍ puede crear expense con su propia categoría
     */
    public function test_user_can_create_expense_with_own_category(): void
    {
        $user = User::create([
            'name' => 'Usuario Válido',
            'email' => 'validuser@test.com',
            'password' => bcrypt('password123'),
        ]);

        $category = Category::create([
            'user_id' => $user->id,
            'name' => 'Mi Categoría',
            'icon' => 'check',
            'color' => '#00FF00',
        ]);

        $response = $this->actingAs($user)->post(route('expenses.store'), [
            'description' => 'Gasto válido',
            'amount' => 50.00,
            'date' => now()->toDateString(),
            'category_id' => $category->id,
        ]);

        $response->assertRedirect(route('expenses.index'));
        $this->assertDatabaseHas('expenses', [
            'description' => 'Gasto válido',
            'category_id' => $category->id,
            'user_id' => $user->id,
        ]);
    }

    /**
     * Test 1.11: Verifica que un usuario NO puede actualizar un expense asignándolo a categoría de otro usuario
     */
    public function test_user_cannot_update_expense_to_another_users_category(): void
    {
        $user1 = User::create([
            'name' => 'Usuario 1',
            'email' => 'user1@update.com',
            'password' => bcrypt('password123'),
        ]);

        $user2 = User::create([
            'name' => 'Usuario 2',
            'email' => 'user2@update.com',
            'password' => bcrypt('password123'),
        ]);

        // Crear categorías para cada usuario
        $category1 = Category::create([
            'user_id' => $user1->id,
            'name' => 'Categoría Usuario 1',
            'icon' => 'test',
            'color' => '#FF0000',
        ]);

        $category2 = Category::create([
            'user_id' => $user2->id,
            'name' => 'Categoría Usuario 2',
            'icon' => 'test',
            'color' => '#00FF00',
        ]);

        // Crear expense para user2 con su propia categoría
        $expense = Expense::create([
            'user_id' => $user2->id,
            'category_id' => $category2->id,
            'description' => 'Gasto original',
            'amount' => 100.00,
            'date' => now()->toDateString(),
        ]);

        // User2 intenta actualizar su expense asignándolo a la categoría de user1
        $response = $this->actingAs($user2)->put(route('expenses.update', $expense->id), [
            'description' => 'Gasto modificado',
            'amount' => 150.00,
            'date' => now()->toDateString(),
            'category_id' => $category1->id, // Intenta usar categoría de otro usuario
        ]);

        // La validación debe fallar
        $response->assertSessionHasErrors(['category_id']);

        // Verificar que el expense no cambió
        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'description' => 'Gasto original',
            'category_id' => $category2->id,
        ]);
    }
}
