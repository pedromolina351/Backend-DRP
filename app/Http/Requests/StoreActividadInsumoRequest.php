<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreActividadInsumoRequest extends FormRequest
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
            'actividades_insumos' => 'required|array|min:1',
            'actividades_insumos.*.codigo_producto_final' => 'required|integer|exists:t_productos_finales,codigo_producto_final',
            'actividades_insumos.*.codigo_producto_intermedio' => 'required|integer|exists:t_productos_intermedios,codigo_producto_intermedio',
            'actividades_insumos.*.actividad' => 'required|string|max:500',
            'actividades_insumos.*.fecha_inicio' => 'string',
            'actividades_insumos.*.fecha_fin' => 'string',
            'actividades_insumos.*.responsable' => 'string',
            'actividades_insumos.*.medio_verificacion' => 'string',
            'actividades_insumos.*.insumo_PACC' => 'nullable|string|max:500',
            'actividades_insumos.*.insumo_no_PACC' => 'nullable|string|max:500',
            'actividades_insumos.*.codigo_objetivo_operativo' => 'required|integer|exists:t_objetivos_operativos,codigo_objetivo_operativo',
        ];
    }

    public function messages()
    {
        return [
            'codigo_poa.required' => 'El campo codigo_poa es obligatorio.',
            'codigo_poa.integer' => 'El campo codigo_poa debe ser un número entero.',
            'codigo_poa.exists' => 'El código POA proporcionado no existe.',
            'codigo_objetivo_operativo.required' => 'El campo codigo_objetivo_operativo es obligatorio.',
            'codigo_objetivo_operativo.integer' => 'El campo codigo_objetivo_operativo debe ser un número entero.',
            'codigo_objetivo_operativo.exists' => 'El código de objetivo operativo proporcionado no existe.',
            'actividades_insumos.required' => 'El campo actividades_insumos es obligatorio.',
            'actividades_insumos.array' => 'El campo actividades_insumos debe ser un arreglo.',
            'actividades_insumos.min' => 'Debe proporcionar al menos una actividad insumo.',
            'actividades_insumos.*.codigo_producto_final.required' => 'El campo codigo_producto_final es obligatorio para cada actividad insumo.',
            'actividades_insumos.*.codigo_producto_final.integer' => 'El campo codigo_producto_final debe ser un número entero.',
            'actividades_insumos.*.codigo_producto_final.exists' => 'El código de producto final proporcionado no existe.',
            'actividades_insumos.*.actividad.required' => 'El campo actividad es obligatorio para cada actividad insumo.',
            'actividades_insumos.*.actividad.string' => 'El campo actividad debe ser una cadena de texto.',
            'actividades_insumos.*.actividad.max' => 'El campo actividad no debe exceder los 500 caracteres.',
            'actividades_insumos.*.insumo_PACC.string' => 'El campo insumo_PACC debe ser una cadena de texto.',
            'actividades_insumos.*.insumo_PACC.max' => 'El campo insumo_PACC no debe exceder los 500 caracteres.',
            'actividades_insumos.*.insumo_no_PACC.string' => 'El campo insumo_no_PACC debe ser una cadena de texto.',
            'actividades_insumos.*.insumo_no_PACC.max' => 'El campo insumo_no_PACC no debe exceder los 500 caracteres.',
        ];
    }
}
