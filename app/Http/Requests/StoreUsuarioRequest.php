<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUsuarioRequest extends FormRequest
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
            'primer_nombre' => 'required|string|max:50',
            'segundo_nombre' => 'required|string|max:50',
            'primer_apellido' => 'required|string|max:50',
            'segundo_apellido' => 'required|string|max:50',
            'dni' => 'required|string|max:50',
            'correo_electronico' => 'required|string',
            'telefono' => 'required|string',
            'codigo_rol' => 'required|integer',
            'codigo_institucion' => 'required|integer',
            'super_user' => 'required|boolean',
            'usuario_drp' => 'required|boolean',
            'estado' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'primer_nombre.required' => 'El primer nombre es requerido',
            'primer_nombre.string' => 'El primer nombre debe ser una cadena de caracteres',
            'primer_nombre.max' => 'El primer nombre debe tener un máximo de 50 caracteres',
            'segundo_nombre.required' => 'El segundo nombre es requerido',
            'segundo_nombre.string' => 'El segundo nombre debe ser una cadena de caracteres',
            'segundo_nombre.max' => 'El segundo nombre debe tener un máximo de 50 caracteres',
            'primer_apellido.required' => 'El primer apellido es requerido',
            'primer_apellido.string' => 'El primer apellido debe ser una cadena de caracteres',
            'primer_apellido.max' => 'El primer apellido debe tener un máximo de 50 caracteres',
            'segundo_apellido.required' => 'El segundo apellido es requerido',
            'segundo_apellido.string' => 'El segundo apellido debe ser una cadena de caracteres',
            'segundo_apellido.max' => 'El segundo apellido debe tener un máximo de 50 caracteres',
            'dni.required' => 'El DNI es requerido',
            'dni.string' => 'El DNI debe ser una cadena de caracteres',
            'dni.max' => 'El DNI debe tener un máximo de 50 caracteres',
            'correo_electronico.required' => 'El correo electrónico es requerido',
            'correo_electronico.string' => 'El correo electrónico debe ser una cadena de caracteres',
            'telefono.required' => 'El teléfono es requerido',
            'telefono.string' => 'El teléfono debe ser una cadena de caracteres',
            'codigo_rol.required' => 'El código del rol es requerido',
            'codigo_rol.integer' => 'El código del rol debe ser un número entero',
            'codigo_institucion.required' => 'El código de la institución es requerido',
            'codigo_institucion.integer' => 'El código de la institución debe ser un número entero',
            'super_user.required' => 'El super usuario es requerido',
            'super_user.boolean' => 'El super usuario debe ser un valor booleano (1 o 0)',
            'usuario_drp.required' => 'El usuario DRP es requerido',
            'usuario_drp.boolean' => 'El usuario DRP debe ser un valor booleano (1 o 0)',
            'estado.required' => 'El estado del usuario es requerido',
            'estado.boolean' => 'El estado del usuario debe ser un valor booleano (1 o 0)',
        ];
    }
}
