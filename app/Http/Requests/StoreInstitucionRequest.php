<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInstitucionRequest extends FormRequest
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
            'nombre_institucion' => 'required|string',
            'codigo_oficial_institucion' => 'required|string|unique:t_instituciones,codigo_oficial_institucion',
            'mision_institucion' => 'required|string',
            'vision_institucion' => 'required|string',
            'certificada' => 'required|boolean',
            'estado_institucion' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre_institucion.required' => 'El nombre de la institución es obligatorio.',
            'codigo_oficial_institucion.required' => 'El código oficial es obligatorio.',
            'certificada.required' => 'El campo "certificada" debe estar definido.',
            'estado_institucion.required' => 'El estado de la institución es obligatorio.',
        ];
    }
}
