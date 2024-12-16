<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InsertObjetivosOperativosRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'codigo_poa' => 'required|integer|exists:poa_t_poas,codigo_poa', // Validar que el código existe en la tabla poa_t_poas
            'listado_objetivos' => 'required|array|min:1',
            'listado_objetivos.*.objetivo_operativo' => 'required|string|max:500', // Validar cada objetivo operativo
            'listado_objetivos.*.subprograma_proyecto' => 'required|string|max:500', // Validar cada subprograma
        ];
    }

    public function messages(): array
    {
        return [
            'codigo_poa.required' => 'El código poa es requerido',
            'codigo_poa.integer' => 'El código poa debe ser un número entero',
            'codigo_poa.exists' => 'El código poa proporcionado no existe.',
            'listado_objetivos.required' => 'La lista de objetivos es requerida',
            'listado_objetivos.array' => 'La lista de objetivos debe ser un arreglo',
            'listado_objetivos.min' => 'La lista de objetivos debe tener al menos un elemento',
            'listado_objetivos.*.objetivo_operativo.required' => 'El objetivo operativo es requerido',
            'listado_objetivos.*.objetivo_operativo.string' => 'El objetivo operativo debe ser una cadena de texto',
            'listado_objetivos.*.objetivo_operativo.max' => 'El objetivo operativo no debe exceder los 500 caracteres',
            'listado_objetivos.*.subprograma_proyecto.required' => 'El subprograma/proyecto es requerido',
            'listado_objetivos.*.subprograma_proyecto.string' => 'El subprograma/proyecto debe ser una cadena de texto',
            'listado_objetivos.*.subprograma_proyecto.max' => 'El subprograma/proyecto no debe exceder los 500 caracteres',
        ];
    }
}
