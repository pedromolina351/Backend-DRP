<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePoliticaRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre_politica_publica' => 'required|string|max:255',
            'descripcion_politica_publica' => 'required|string',
            'estado_politica_publica' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre_politica_publica.required' => 'El nombre de la política pública es requerido',
            'nombre_politica_publica.string' => 'El nombre de la política pública debe ser una cadena de caracteres',
            'nombre_politica_publica.max' => 'El nombre de la política pública no debe exceder los 255 caracteres',
            'descripcion_politica_publica.required' => 'La descripción de la política pública es requerida',
            'descripcion_politica_publica.string' => 'La descripción de la política pública debe ser una cadena de caracteres',
            'estado_politica_publica.boolean' => 'El estado de la política pública debe ser un valor booleano',
        ];
    }
}
