<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePoaRequest extends FormRequest
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
            'nombre_poa' => 'required|string|max:255',
            'descripcion_poa' => 'nullable|string',
            'codigo_institucion' => 'required|integer', //|exists:t_instituciones,codigo_institucion',
            'codigo_programa' => 'required|integer', //|exists:t_programas,codigo_programa',
            'codigo_usuario_crea' => 'required|integer', //|exists:users,id',
            'fecha_poa' => 'date',
            'estado_poa' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre_poa.required' => 'El nombre del poa es obligatorio.',
            'estado_poa.required' => 'El estado del poa es obligatorio.',
            'codigo_institucion.required' => 'El código de la institución es obligatorio.',
            'codigo_programa.required' => 'El código del programa es obligatorio.',
            'codigo_usuario_crea.required' => 'El código del usuario creador es obligatorio.',
            'codigo_institucion.exists' => 'El código de la institución no existe.',
            'codigo_programa.exists' => 'El código del programa no existe.',
            'codigo_usuario_crea.exists' => 'El código del usuario creador no existe.'
        ];
    }
}
