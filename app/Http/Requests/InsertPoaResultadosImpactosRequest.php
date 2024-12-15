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
            // 'codigo_poa'=> 'required|integer',
            // 'codigo_resultado_final'=> 'required|integer',
            // 'codigo_indicador_resultado_final'=> 'required|integer',
            // 'codigo_resultado'=> 'required|integer'
        ];
    }

    public function messages(): array
    {
        return [
            // 'codigo_poa.required' => 'El código poa es requerido',
            // 'codigo_poa.integer' => 'El código poa debe ser un número entero',
            // 'codigo_resultado_final.required' => 'El código resultado final es requerido',
            // 'codigo_resultado_final.integer' => 'El código resultado final debe ser un número entero',
            // 'codigo_indicador_resultado_final.required' => 'El código indicador resultado final es requerido',
            // 'codigo_indicador_resultado_final.integer' => 'El código indicador resultado final debe ser un número entero',
            // 'codigo_resultado.required' => 'El código resultado es requerido',
            // 'codigo_resultado.integer' => 'El código resultado debe ser un número entero',
        ];
    }
}
