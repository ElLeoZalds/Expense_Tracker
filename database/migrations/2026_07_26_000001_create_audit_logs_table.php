<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action'); // created, updated, deleted, restored, force_deleted
            $table->string('model_type'); // Clase completa del modelo (ej: App\Models\Expense)
            $table->unsignedBigInteger('model_id'); // ID del modelo afectado
            $table->json('old_values')->nullable(); // Valores anteriores en formato JSON
            $table->json('new_values')->nullable(); // Valores nuevos en formato JSON
            $table->string('ip_address', 45)->nullable(); // IP del usuario que realizó la acción
            $table->text('user_agent')->nullable(); // User agent del navegador/cliente
            $table->timestamps();

            // Índices para consultas eficientes
            $table->index(['model_type', 'model_id']);
            $table->index('user_id');
            $table->index('action');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
