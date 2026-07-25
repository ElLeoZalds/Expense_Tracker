<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBudgetRequest extends FormRequest
{
    /**
     * Determinar si el usuario está autorizado para hacer esta solicitud.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Obtener las reglas de validación aplicables a la solicitud.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => ['nullable', 'exists:categories,id'],
            'amount_limit' => ['sometimes', 'numeric', 'min:0.01', 'max:999999999.99'],
            'month' => ['sometimes', 'integer', 'between:1,12'],
            'year' => ['sometimes', 'integer', 'between:2020,2100'],
        ];
    }

    /**
     * Obtener mensajes de error personalizados para las reglas de validación.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'amount_limit.numeric' => 'El límite debe ser un número válido.',
            'amount_limit.min' => 'El límite debe ser mayor a cero.',
            'month.between' => 'El mes debe estar entre 1 y 12.',
            'year.between' => 'El año debe estar entre 2020 y 2100.',
        ];
    }
}