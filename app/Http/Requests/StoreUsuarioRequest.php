<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class StoreUsuarioRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }


    public function rules()
    {
        return [
            'primer_nombre' => 'required|string|max:50',
            'segundo_nombre' => 'nullable|string|max:50',
            'primer_apellido' => 'required|string|max:50',
            'segundo_apellido' => 'required|string|max:50',
            'dni' => 'required|string|max:50|unique:config_t_usuarios,dni',
            'correo_electronico' => 'required|email|max:255|unique:config_t_usuarios,correo_electronico',
            'telefono' => 'required|string|max:50',
            'codigo_rol' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    if (!DB::table('roles.t_roles')->where('codigo_rol', $value)->exists()) {
                        $fail('El código de rol no existe.');
                    }
                },
            ],
            'codigo_institucion' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    if (!DB::table('t_instituciones')->where('codigo_institucion', $value)->exists()) {
                        $fail('El código de institución no existe.');
                    }
                },
            ],
            'super_user' => 'nullable|boolean',
            'usuario_drp' => 'nullable|boolean',
            'estado' => 'nullable|boolean',
            'password' => 'required|string|min:8',
            'url_img_perfil' => 'string',
        ];
    }
    
    public function messages()
    {
        return [
            'primer_nombre.required' => 'El primer nombre es obligatorio.',
            'primer_nombre.string' => 'El primer nombre debe ser una cadena de texto.',
            'primer_nombre.max' => 'El primer nombre no puede exceder los 50 caracteres.',
            'dni.required' => 'El DNI es obligatorio.',
            'dni.unique' => 'El DNI ya está registrado.',
            'correo_electronico.required' => 'El correo electrónico es obligatorio.',
            'correo_electronico.email' => 'El correo electrónico debe tener un formato válido.',
            'correo_electronico.unique' => 'El correo electrónico ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
        ];
    }
}
