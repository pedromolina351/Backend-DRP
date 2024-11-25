<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre_rol' => 'required|string|max:50',
            'descripcion_rol' => 'required|string|max:255',
            'estado' => 'boolean'
        ]; 
    }

    public function messages(): array
    {
        return [
            'nombre_rol.required' => 'El nombre del rol es requerido',
            'nombre_rol.string' => 'El nombre del rol debe ser una cadena de caracteres',
            'nombre_rol.max' => 'El nombre del rol debe tener un máximo de 50 caracteres',
            'descripcion_rol.required' => 'La descripción del rol es requerida',
            'descripcion_rol.string' => 'La descripción del rol debe ser una cadena de caracteres',
            'descripcion_rol.max' => 'La descripción del rol debe tener un máximo de 255 caracteres',            'estado.boolean' => 'El estado del rol debe ser un valor booleano',
        ];
    }
}
