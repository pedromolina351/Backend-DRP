<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

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
            'nombre_rol' => [
                'required',
                'string',
                'max:100',
                function ($attribute, $value, $fail) {
                    if (DB::table('roles.t_roles')->where('nombre_rol', $value)->exists()) {
                        $fail('El nombre del rol ya existe.');
                    }
                },
            ],
            'descripcion_rol' => 'nullable|string|max:255',
            'estado_rol' => 'required|integer|in:0,1',
            'editar' => 'nullable|integer|in:0,1',
            'codigos_acceso_modulo' => [
                'required',
                'string'
            ],
        ];
    }

    public function messages()
    {
        return [
            'nombre_rol.required' => 'El nombre del rol es obligatorio.',
            'nombre_rol.string' => 'El nombre del rol debe ser una cadena de texto.',
            'nombre_rol.max' => 'El nombre del rol no puede exceder los 100 caracteres.',
            'descripcion_rol.string' => 'La descripción del rol debe ser una cadena de texto.',
            'descripcion_rol.max' => 'La descripción del rol no puede exceder los 255 caracteres.',
            'estado_rol.required' => 'El estado del rol es obligatorio.',
            'estado_rol.integer' => 'El estado del rol debe ser un número entero.',
            'estado_rol.in' => 'El estado del rol debe ser 0 (inactivo) o 1 (activo).',
            'codigos_acceso_modulo.required' => 'Debe proporcionar al menos un código de acceso.',
            'codigos_acceso_modulo.string' => 'Los códigos de acceso deben estar en formato de texto.',
        ];
    }

}
