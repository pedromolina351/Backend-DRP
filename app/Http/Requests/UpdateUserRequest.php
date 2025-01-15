<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class UpdateUserRequest extends FormRequest
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
            'codigo_usuario' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    if (!DB::table('config_t_usuarios')->where('codigo_usuario', $value)->exists()) {
                        $fail('El código de usuario no existe.');
                    }
                },
            ],
            'primer_nombre' => 'nullable|string|max:50',
            'segundo_nombre' => 'nullable|string|max:50',
            'primer_apellido' => 'nullable|string|max:50',
            'segundo_apellido' => 'nullable|string|max:50',
            'dni' => [
                'nullable',
                'string',
                'max:50',
                function ($attribute, $value, $fail) {
                    if (DB::table('config_t_usuarios')->where('dni', $value)->where('codigo_usuario', '!=', request()->codigo_usuario)->exists()) {
                        $fail('El DNI ya está registrado para otro usuario.');
                    }
                },
            ],
            'correo_electronico' => [
                'nullable',
                'email',
                'max:255',
                function ($attribute, $value, $fail) {
                    if (DB::table('config_t_usuarios')->where('correo_electronico', $value)->where('codigo_usuario', '!=', request()->codigo_usuario)->exists()) {
                        $fail('El correo electrónico ya está registrado para otro usuario.');
                    }
                },
            ],
            'telefono' => 'nullable|string|max:50',
            'codigo_rol' => [
                'nullable',
                'integer',
                function ($attribute, $value, $fail) {
                    if (!DB::table('roles.t_roles')->where('codigo_rol', $value)->exists()) {
                        $fail('El código de rol no existe.');
                    }
                },
            ],
            'codigo_institucion' => [
                'nullable',
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
            'password' => 'nullable|string|min:8',
            'url_img_perfil' => 'nullable|string'
        ];
    }
    
    public function messages()
    {
        return [
            'codigo_usuario.required' => 'El código de usuario es obligatorio.',
            'codigo_usuario.integer' => 'El código de usuario debe ser un número entero.',
            'primer_nombre.string' => 'El primer nombre debe ser una cadena de texto.',
            'primer_nombre.max' => 'El primer nombre no puede exceder los 50 caracteres.',
            'correo_electronico.email' => 'El correo electrónico debe ser válido.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
        ];
    }
}
