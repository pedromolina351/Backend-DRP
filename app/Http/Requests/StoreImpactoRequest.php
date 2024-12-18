<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreImpactoRequest extends FormRequest
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
             'codigo_poa' => 'required|integer|exists:poa_t_poas,codigo_poa',
             'impactos' => 'required|array|min:1',
             'impactos.*.codigo_resultado_final' => 'required|integer|exists:t_resultado_final,codigo_resultado_final',
             'impactos.*.codigo_indicador_resultado_final' => 'required|integer|exists:t_indicador_resultado_final,codigo_indicador_resultado_final',
         ];
     }
 
     public function messages()
     {
         return [
             'codigo_poa.required' => 'El campo codigo_poa es obligatorio.',
             'codigo_poa.integer' => 'El campo codigo_poa debe ser un número entero.',
             'codigo_poa.exists' => 'El código POA proporcionado no existe.',
             'impactos.required' => 'Debe proporcionar al menos un impacto.',
             'impactos.array' => 'El campo impactos debe ser un arreglo.',
             'impactos.min' => 'Debe proporcionar al menos un impacto.',
             'impactos.*.codigo_resultado_final.required' => 'El campo codigo_resultado_final es obligatorio para cada impacto.',
             'impactos.*.codigo_resultado_final.integer' => 'El campo codigo_resultado_final debe ser un número entero.',
             'impactos.*.codigo_resultado_final.exists' => 'El código de resultado final proporcionado no existe.',
             'impactos.*.codigo_indicador_resultado_final.required' => 'El campo codigo_indicador_resultado_final es obligatorio para cada impacto.',
             'impactos.*.codigo_indicador_resultado_final.integer' => 'El campo codigo_indicador_resultado_final debe ser un número entero.',
             'impactos.*.codigo_indicador_resultado_final.exists' => 'El código de indicador de resultado final proporcionado no existe.',
         ];
     }
}
