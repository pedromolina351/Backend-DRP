<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class changePasswordRequest extends FormRequest
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
            'correo_electronico' => 'required|email',
            'nuevo_password' => 'required|string'
        ];
    }

    public function messages()
    {
        return [
            'correo_electronico.required' => 'El correo electrónico es obligatorio.',
            'correo_electronico.email' => 'Debe ingresar un correo electrónico válido.',
            'nuevo_password.required' => 'La nueva contraseña es obligatoria.',
            'nuevo_password.string' => 'La nueva contraseña debe ser un texto válido.'
        ];
    }
}
