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
            'objetivos_operativo' => 'nullable', 
            'subprogramas_proyecto' => 'nullable',
        ];
    }

    public function messages(): array
    {
        return [
            'codigo_poa.required' => 'El código poa es requerido',
            'codigo_poa.integer' => 'El código poa debe ser un número entero',
            'codigo_poa.exists' => 'El código poa proporcionado no existe.',
            'objetivos_operativo.required' => 'El objetivo operativo es requerido',
            'objetivos_operativo.string' => 'El objetivo operativo debe ser una cadena de texto',
            'subprogramas_proyecto.required' => 'El subprograma/proyecto es requerido',
            'subprogramas_proyecto.string' => 'El subprograma/proyecto debe ser una cadena de texto',
        ];
    }
}
