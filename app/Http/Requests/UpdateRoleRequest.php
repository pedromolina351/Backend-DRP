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
            'listado_accesos' => 'required|array|min:1', // Validar que listado_accesos sea un array con al menos un elemento
            'listado_accesos.*.codigo_acceso_modulo' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    if (!DB::table('roles.t_accesos_modulos')->where('codigo_acceso_modulo', $value)->exists()) {
                        $fail("El código de acceso del módulo {$value} no existe.");
                    }
                },
            ],
            'listado_accesos.*.estado_rol_acceso' => 'required|integer|in:0,1', // Validar que el estado sea 0 o 1
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
            'listado_accesos.required' => 'El listado de accesos es obligatorio.',
            'listado_accesos.array' => 'El listado de accesos debe ser un arreglo.',
            'listado_accesos.min' => 'Debe proporcionar al menos un acceso en el listado de accesos.',
            'listado_accesos.*.codigo_acceso_modulo.required' => 'El código del acceso del módulo es obligatorio.',
            'listado_accesos.*.codigo_acceso_modulo.integer' => 'El código del acceso del módulo debe ser un número entero.',
            'listado_accesos.*.estado_rol_acceso.required' => 'El estado del acceso del rol es obligatorio.',
            'listado_accesos.*.estado_rol_acceso.integer' => 'El estado del acceso del rol debe ser un número entero.',
            'listado_accesos.*.estado_rol_acceso.in' => 'El estado del acceso del rol debe ser 0 (inactivo) o 1 (activo).',
        ];
    }
}
