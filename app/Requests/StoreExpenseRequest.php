<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validación para crear un nuevo expense.
 */
class StoreExpenseRequest extends FormRequest
{
    /**
     * Determinar si el usuario está autorizado para hacer esta solicitud.
     */
    public function authorize(): bool
    {
        return true; // Se valida ownership en el controlador
    }

    /**
     * Obtener las reglas de validación aplicables a la solicitud.
     */
    public function rules(): array
    {
        return [
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'category_id' => 'required|exists:categories,id',
            'notes' => 'nullable|string',
        ];
    }

    /**
     * Mensajes de error personalizados (opcional).
     */
    public function messages(): array
    {
        return [
            'description.required' => 'La descripción es obligatoria.',
            'amount.required' => 'El monto es obligatorio.',
            'amount.min' => 'El monto debe ser mayor a 0.',
            'date.required' => 'La fecha es obligatoria.',
            'category_id.required' => 'Debe seleccionar una categoría.',
            'category_id.exists' => 'La categoría seleccionada no existe.',
        ];
    }
}
