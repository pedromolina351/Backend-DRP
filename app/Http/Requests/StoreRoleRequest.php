<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'nombre_rol' => 'required|string|max:100',
            'descripcion_rol' => 'nullable|string|max:255',
            'estado_rol' => 'nullable|integer|in:0,1', // Estados válidos: 0 (Inactivo) o 1 (Activo)
        ];
    }
    
    public function messages()
    {
        return [
            'nombre_rol.required' => 'El nombre del rol es obligatorio.',
            'nombre_rol.string' => 'El nombre del rol debe ser una cadena de texto.',
            'nombre_rol.max' => 'El nombre del rol no puede exceder los 100 caracteres.',
            'nombre_rol.unique' => 'El nombre del rol ya existe.',
            'descripcion_rol.string' => 'La descripción del rol debe ser una cadena de texto.',
            'descripcion_rol.max' => 'La descripción del rol no puede exceder los 255 caracteres.',
            'estado_rol.integer' => 'El estado del rol debe ser un número entero.',
            'estado_rol.in' => 'El estado del rol debe ser 0 (inactivo) o 1 (activo).',
        ];
    }
}
