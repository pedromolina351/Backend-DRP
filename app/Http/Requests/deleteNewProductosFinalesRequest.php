<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class deleteNewProductosFinalesRequest extends FormRequest
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
            'productos_finales' => 'required|array',
            'productos_finales.*codigo_producto_final' => 'required|integer|exists:t_productos_finales,codigo_producto_final',
        ];
    }

    public function messages(): array
    {
        return [
            'codigo_poa.required' => 'El código del POA es requerido',
            'codigo_poa.integer' => 'El código del POA debe ser un número entero',
            'productos_finales.required' => 'Los productos finales son requeridos',
            'productos_finales.array' => 'Los productos finales deben ser un arreglo',
            'productos_finales.*.codigo_producto_final.required' => 'El código del producto final es requerido',
            'productos_finales.*.codigo_producto_final.integer' => 'El código del producto final debe ser un número entero',
            'productos_finales.*.codigo_producto_final.exists' => 'El código del producto final no existe',
        ];
    }
}
