<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class UpdateRoleRequest extends FormRequest
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
            'codigo_rol' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    if (!DB::table('roles.t_roles')->where('codigo_rol', $value)->exists()) {
                        $fail('El código del rol no existe.');
                    }
                },
            ],
            'nombre_rol' => 'nullable|string|max:100',
            'descripcion_rol' => 'nullable|string|max:255',
            'estado_rol' => 'nullable|integer|in:0,1', // Suponiendo que los estados válidos son 0 (inactivo) y 1 (activo)
            'editar' => 'nullable|integer|in:0,1',
            'codigos_acceso_modulo' => 'required|string'    
        ];
    }
    
    /**
     * Mensajes personalizados para los errores de validación.
     */
    public function messages()
    {
        return [
            'codigo_rol.required' => 'El código del rol es obligatorio.',
            'codigo_rol.integer' => 'El código del rol debe ser un número entero.',
            'nombre_rol.string' => 'El nombre del rol debe ser una cadena de texto.',
            'nombre_rol.max' => 'El nombre del rol no puede exceder los 100 caracteres.',
            'descripcion_rol.string' => 'La descripción del rol debe ser una cadena de texto.',
            'descripcion_rol.max' => 'La descripción del rol no puede exceder los 255 caracteres.',
            'estado_rol.integer' => 'El estado del rol debe ser un número entero.',
            'estado_rol.in' => 'El estado del rol debe ser 0 (inactivo) o 1 (activo).',
        ];
    }
}
