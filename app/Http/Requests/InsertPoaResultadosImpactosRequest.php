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
            'codigo_poa' => 'required|integer|exists:poa_t_poas,codigo_poa',
            'codigos_resultado_final' => 'required|string',
            'codigos_indicador_resultado_final' => 'required|string',
            'codigos_resultado' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'codigo_poa.required' => 'El código poa es requerido',
            'codigo_poa.integer' => 'El código poa debe ser un número entero',
            'codigo_poa.exists' => 'El código poa no existe',
            'codigos_resultado_final.required' => 'Los códigos de resultado final son requerido',
            'codigos_indicador_resultado_final.required' => 'Los códigos de indicador resultado final son requeridos',
            'codigos_resultado.required' => 'Los códigos de resultado son requeridos',
        ];
    }
}
