<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CorreoCambioRequest extends FormRequest
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
            'codigo_usuario' => 'required|integer|exists:config_t_usuarios,codigo_usuario',
            'accion' => 'required|integer|in:1,2,3',
            'mensaje_adicional' => 'nullable|string|max:500'
        ];
    }

    public function messages()
    {
        return [
            'codigo_usuario.required' => 'El código de usuario es obligatorio.',
            'codigo_usuario.integer' => 'El código de usuario debe ser un número entero.',
            'codigo_usuario.exists' => 'El código de usuario especificado no existe.',
            'accion.required' => 'La acción es obligatoria.',
            'accion.integer' => 'La acción debe ser un número entero.',
            'accion.in' => 'La acción debe ser 1 (Registro), 2 (Login) o 3 (Reseteo de contraseña).',
            'mensaje_adicional.string' => 'El mensaje adicional debe ser un texto válido.',
            'mensaje_adicional.max' => 'El mensaje adicional no puede superar los 500 caracteres.'
        ];
    }
}
