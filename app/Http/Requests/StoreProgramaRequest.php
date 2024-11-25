<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProgramaRequest extends FormRequest
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
        $fechaActual = date('d-m-Y');
        return [
            'nombre_programa' => 'required|string|max:50',
            'descripcion_programa' => 'required|string|max:255',
            'objetivo_programa' => 'required|string|max:255',
            'codigo_oficial_programa' => 'required|integer',
            'codigo_institucion' => 'required|integer', //|exists:t_instituciones,codigo_institucion',
            'estado_programa' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre_programa.required' => 'El nombre del programa es requerido',
            'nombre_programa.string' => 'El nombre del programa debe ser una cadena de caracteres',
            'nombre_programa.max' => 'El nombre del programa debe tener un máximo de 50 caracteres',
            'descripcion_programa.required' => 'La descripción del programa es requerida',
            'descripcion_programa.string' => 'La descripción del programa debe ser una cadena de caracteres',
            'descripcion_programa.max' => 'La descripción del programa debe tener un máximo de 255 caracteres',
            'objetivo_programa.required' => 'El objetivo del programa es requerido',
            'objetivo_programa.string' => 'El objetivo del programa debe ser una cadena de caracteres',
            'objetivo_programa.max' => 'El objetivo del programa debe tener un máximo de 255 caracteres',
            'codigo_oficial_programa.required' => 'El código oficial del programa es requerido',
            'codigo_oficial_programa.integer' => 'El código oficial del programa debe ser un número entero',
            'codigo_institucion.required' => 'El código de la institución es requerido',
            'codigo_institucion.integer' => 'El código de la institución debe ser un número entero',
            'codigo_institucion.exists' => 'El código de la institución no existe en la tabla de instituciones',
        ];
    }


}
