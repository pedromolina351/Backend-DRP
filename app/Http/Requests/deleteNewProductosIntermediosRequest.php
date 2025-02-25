<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class deleteNewProductosIntermediosRequest extends FormRequest
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
            'productos_intermedios' => 'required|array',
            'productos_intermedios.*.codigo_producto_intermedio' => 'required|integer|exists:t_productos_intermedios,codigo_producto_intermedio',
        ];
    }

    public function messages(): array
    {
        return [
            'codigo_poa.required' => 'El código del POA es requerido',
            'codigo_poa.integer' => 'El código del POA debe ser un número entero',
            'productos_intermedios.required' => 'Los productos intermedios son requeridos',
            'productos_intermedios.array' => 'Los productos intermedios deben ser un arreglo',
            'productos_intermedios.*.codigo_producto_intermedio.required' => 'El código del producto intermedio es requerido',
            'productos_intermedios.*.codigo_producto_intermedio.integer' => 'El código del producto intermedio debe ser un número entero',
            'productos_intermedios.*.codigo_producto_intermedio.exists' => 'El código del producto intermedio no existe',
        ];
    }
}
