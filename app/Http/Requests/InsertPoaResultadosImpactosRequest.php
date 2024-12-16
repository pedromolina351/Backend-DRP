<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InsertPoaResultadosImpactosRequest extends FormRequest
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
            'codigo_poa' => 'required|integer',
            'codigo_resultado_final' => 'required|integer',
            'codigo_indicador_resultado_final' => 'required|integer',
            'codigo_resultado' => 'required|integer',
            'listado_impactos' => 'required|array|min:1',
            'listado_impactos.*.impacto' => 'required|string|max:500', // Validar cada impacto
            'listado_impactos.*.efecto' => 'required|string|max:500', // Validar cada efecto
            'listado_impactos.*.indicador_impacto' => 'required|string|max:500', // Validar cada indicador de impacto
            'listado_impactos.*.meta_impacto' => 'required|string|max:500', // Validar cada meta de impacto
            'listado_impactos.*.unidad_medida_impacto' => 'required|string|max:500', // Validar cada unidad de medida de impacto
            'listado_impactos.*.codigo_resultado_final' => 'required|integer', // Validar cada código de resultado final
            'listado_impactos.*.codigo_indicador_resultado_final' => 'required|integer', // Validar cada código de indicador de resultado final
            'listado_impactos.*.codigo_resultado' => 'required|integer', // Validar cada código de resultado
        ];
    }

    public function messages(): array
    {
        return [
            'codigo_poa.required' => 'El código poa es requerido',
            'codigo_poa.integer' => 'El código poa debe ser un número entero',
            'codigo_resultado_final.required' => 'El código resultado final es requerido',
            'codigo_resultado_final.integer' => 'El código resultado final debe ser un número entero',
            'codigo_indicador_resultado_final.required' => 'El código indicador resultado final es requerido',
            'codigo_indicador_resultado_final.integer' => 'El código indicador resultado final debe ser un número entero',
            'codigo_resultado.required' => 'El código resultado es requerido',
            'codigo_resultado.integer' => 'El código resultado debe ser un número entero',
            'listado_impactos.required' => 'La lista de impactos es requerida',
            'listado_impactos.array' => 'La lista de impactos debe ser un arreglo',
            'listado_impactos.min' => 'La lista de impactos debe tener al menos un elemento',
            'listado_impactos.*.impacto.required' => 'El impacto es requerido',
            'listado_impactos.*.impacto.string' => 'El impacto debe ser una cadena de texto',
            'listado_impactos.*.impacto.max' => 'El impacto no debe exceder los 500 caracteres',
            'listado_impactos.*.efecto.required' => 'El efecto es requerido',
            'listado_impactos.*.efecto.string' => 'El efecto debe ser una cadena de texto',
            'listado_impactos.*.efecto.max' => 'El efecto no debe exceder los 500 caracteres',
            'listado_impactos.*.indicador_impacto.required' => 'El indicador de impacto es requerido',
            'listado_impactos.*.indicador_impacto.string' => 'El indicador de impacto debe ser una cadena de texto',
            'listado_impactos.*.indicador_impacto.max' => 'El indicador de impacto no debe exceder los 500 caracteres',
            'listado_impactos.*.meta_impacto.required' => 'La meta de impacto es requerida',
            'listado_impactos.*.meta_impacto.string' => 'La meta de impacto debe ser una cadena de texto',
            'listado_impactos.*.meta_impacto.max' => 'La meta de impacto no debe exceder los 500 caracteres',
            'listado_impactos.*.unidad_medida_impacto.required' => 'La unidad de medida de impacto es requerida',
            'listado_impactos.*.unidad_medida_impacto.string' => 'La unidad de medida de impacto debe ser una cadena de texto',
            'listado_impactos.*.unidad_medida_impacto.max' => 'La unidad de medida de impacto no debe exceder los 500 caracteres',
            'listado_impactos.*.codigo_resultado_final.required' => 'El código de resultado final es requerido',
            'listado_impactos.*.codigo_resultado_final.integer' => 'El código de resultado final debe ser un número entero',
            'listado_impactos.*.codigo_indicador_resultado_final.required' => 'El código de indicador de resultado final es requerido',
            'listado_impactos.*.codigo_indicador_resultado_final.integer' => 'El código de indicador de resultado final debe ser un número entero',
            'listado_impactos.*.codigo_resultado.required' => 'El código de resultado es requerido'
        ];
    }
}
