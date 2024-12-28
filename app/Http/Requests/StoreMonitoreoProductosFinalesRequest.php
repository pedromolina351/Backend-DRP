<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMonitoreoProductosFinalesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
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
            'codigo_producto_final' => 'required|integer|exists:t_productos_finales,codigo_producto_final',
            'nombre_unidad_organizativa' => 'required|string|max:100',
            'nombre_responsable_unidad_organizativa' => 'required|string|max:100',
            'codigo_unidad_medida' => 'required|integer|exists:mmr.t_unidad_medida,codigo_unidad_medida',
            'codigo_tipo_indicador' => 'required|integer|exists:mmr.tipo_indicador,codigo_tipo_indicador',
            'codigo_categorizacion' => 'required|integer|exists:mmr.t_categorizacion,codigo_categorizacion',
            'medio_verificacion' => 'required|string|max:100',
            'fuente_financiamiento' => 'required|string|max:100',
            'meta_cantidad_anual' => 'required|integer|min:1',
            'codigo_tipo_riesgo' => 'required|integer|exists:mmr.t_tipo_riesgo,codigo_tipo_riesgo',
            'codigo_nivel_impacto' => 'required|integer|exists:mmr.t_nivel_impacto,codigo_nivel_impacto',
            'descripcion_riesgo' => 'nullable|string',
        ];
    }

    /**
     * Mensajes de error personalizados para la validación.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'codigo_poa.required' => 'El código POA es obligatorio.',
            'codigo_poa.integer' => 'El código POA debe ser un número entero.',
            'codigo_poa.exists' => 'El código POA no existe en la base de datos.',

            'codigo_producto_final.required' => 'El código del producto final es obligatorio.',
            'codigo_producto_final.integer' => 'El código del producto final debe ser un número entero.',
            'codigo_producto_final.exists' => 'El código del producto final no existe en la base de datos.',

            'nombre_unidad_organizativa.required' => 'El nombre de la unidad organizativa es obligatorio.',
            'nombre_unidad_organizativa.max' => 'El nombre de la unidad organizativa no debe exceder los 100 caracteres.',

            'nombre_responsable_unidad_organizativa.required' => 'El nombre del responsable de la unidad organizativa es obligatorio.',
            'nombre_responsable_unidad_organizativa.max' => 'El nombre del responsable de la unidad organizativa no debe exceder los 100 caracteres.',

            'codigo_unidad_medida.required' => 'El código de la unidad de medida es obligatorio.',
            'codigo_unidad_medida.exists' => 'El código de la unidad de medida no existe en la base de datos.',

            'codigo_tipo_indicador.required' => 'El código del tipo de indicador es obligatorio.',
            'codigo_tipo_indicador.exists' => 'El código del tipo de indicador no existe en la base de datos.',

            'codigo_categorizacion.required' => 'El código de categorización es obligatorio.',
            'codigo_categorizacion.exists' => 'El código de categorización no existe en la base de datos.',

            'medio_verificacion.required' => 'El medio de verificación es obligatorio.',
            'medio_verificacion.max' => 'El medio de verificación no debe exceder los 100 caracteres.',

            'fuente_financiamiento.required' => 'La fuente de financiamiento es obligatoria.',
            'fuente_financiamiento.max' => 'La fuente de financiamiento no debe exceder los 100 caracteres.',

            'meta_cantidad_anual.required' => 'La meta de cantidad anual es obligatoria.',
            'meta_cantidad_anual.integer' => 'La meta de cantidad anual debe ser un número entero.',
            'meta_cantidad_anual.min' => 'La meta de cantidad anual debe ser mayor a 0.',

            'codigo_tipo_riesgo.required' => 'El código del tipo de riesgo es obligatorio.',
            'codigo_tipo_riesgo.exists' => 'El código del tipo de riesgo no existe en la base de datos.',

            'codigo_nivel_impacto.required' => 'El código del nivel de impacto es obligatorio.',
            'codigo_nivel_impacto.exists' => 'El código del nivel de impacto no existe en la base de datos.',

            'descripcion_riesgo.string' => 'La descripción del riesgo debe ser un texto válido.',
        ];
    }
}
