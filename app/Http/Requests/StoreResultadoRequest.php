<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreResultadoRequest extends FormRequest
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
            'codigo_poa' => 'required|integer|max:255',
            'Resultados' => 'required|array|min:1',
            'Resultados.*.resultado_institucional' => 'required|string',
            'Resultados.*.indicador_resultado_institucional' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'Resultados.required' => 'El campo Resultados es obligatorio.',
            'Resultados.array' => 'El campo Resultados debe ser un arreglo.',
            'Resultados.min' => 'Debe proporcionar al menos un resultado.',
            'Resultados.*.resultado_institucional.required' => 'El campo resultado_institucional es obligatorio para cada resultado.',
            'Resultados.*.resultado_institucional.string' => 'El campo resultado_institucional debe ser una cadena de texto.',
            'Resultados.*.indicador_resultado_institucional.required' => 'El campo indicador_resultado_institucional es obligatorio para cada resultado.',
            'Resultados.*.indicador_resultado_institucional.string' => 'El campo indicador_resultado_institucional debe ser una cadena de texto.',
        ];
    }
}
