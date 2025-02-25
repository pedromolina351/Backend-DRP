<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class deleteActividadesInsumosRequest extends FormRequest
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
            "codigo_poa" => "required|integer",
            "actividades_insumos" => "required|array",
            "actividades_insumos.*codigo_actividad_insumo" => "required|integer",
        ];
    }

    public function messages(): array
    {
        return [
            "codigo_poa.required" => "El código del POA es requerido",
            "codigo_poa.integer" => "El código del POA debe ser un número entero",
            "actividades_insumos.required" => "Las actividades e insumos son requeridos",
            "actividades_insumos.array" => "Las actividades e insumos deben ser un arreglo",
            "actividades_insumos.*.codigo_actividad_insumo.required" => "El código de la actividad o insumo es requerido",
            "actividades_insumos.*.codigo_actividad_insumo.integer" => "El código de la actividad o insumo debe ser un número entero",
        ];
    }
}
