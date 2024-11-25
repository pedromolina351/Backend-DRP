<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreModuloRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre_modulo' => 'required|string',
            'estado' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre_modulo.required' => 'El nombre del módulo es requerido',
            'nombre_modulo.string' => 'El nombre del módulo debe ser una cadena de caracteres',
            'estado.required' => 'El estado del módulo es requerido',
            'estado.boolean' => 'El estado del módulo debe ser un valor booleano (1 o 0)',
        ];
    }
}
