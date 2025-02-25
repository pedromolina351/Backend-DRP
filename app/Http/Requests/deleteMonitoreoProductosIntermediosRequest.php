<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class deleteMonitoreoProductosIntermediosRequest extends FormRequest
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
            //
            'codigo_poa' => 'required|integer',
            'monitoreos' => 'required|array',
            'monitoreos.*codigo_monitoreo_producto_intermedio' => 'required|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'codigo_poa.required' => 'El código del POA es requerido',
            'codigo_poa.integer' => 'El código del POA debe ser un número entero',
            'monitoreos.required' => 'Los monitoreos son requeridos',
            'monitoreos.array' => 'Los monitoreos deben ser un arreglo',
            'monitoreos.*.codigo_monitoreo_producto_intermedio.required' => 'El código del monitoreo es requerido',
            'monitoreos.*.codigo_monitoreo_producto_intermedio.integer' => 'El código del monitoreo debe ser un número entero',
        ];
    }
}
