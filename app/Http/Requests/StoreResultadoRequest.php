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
    public function rules(): array
    {
        return [
            'resultado_institucional' => 'required|string',
            'indicador_resultado_institucional' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'resultado_institucional.required' => 'El resultado institucional es requerido',
            'indicador_resultado_institucional.required' => 'El indicador del resultado institucional es requerido',
            'resultado_institucional.string' => 'El resultado institucional debe ser una cadena de texto',
            'indicador_resultado_institucional.string' => 'El indicador del resultado institucional debe ser una cadena de texto',
        ];
    }
}
