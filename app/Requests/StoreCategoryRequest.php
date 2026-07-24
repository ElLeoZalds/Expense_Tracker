<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validación para crear una nueva categoría.
 */
class StoreCategoryRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:categories,name',
            'icon' => 'nullable|string|max:10',
            'color' => 'nullable|string|max:7',
        ];
    }

    /**
     * Mensajes de error personalizados (opcional).
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.unique' => 'Ya existe una categoría con ese nombre.',
        ];
    }
}