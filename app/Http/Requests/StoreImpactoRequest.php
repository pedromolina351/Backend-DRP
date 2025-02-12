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
            'codigos_resultado_final' => 'nullable',
            'codigos_indicador_resultado_final' => 'nullable',
        ];
    }

    public function messages()
    {
        return [
            'codigo_poa.required' => 'El campo codigo_poa es obligatorio.',
            'codigo_poa.integer' => 'El campo codigo_poa debe ser un número entero.',
            'codigo_poa.exists' => 'El código POA proporcionado no existe.',
        ];
    }
}
