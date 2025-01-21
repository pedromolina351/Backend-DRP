<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class StoreMonitoreoProductosFinalesRequest extends FormRequest
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
            'listado_monitoreo' => 'required|array|min:1',
            'listado_monitoreo.*.codigo_producto_final' => 'required|integer|exists:t_productos_finales,codigo_producto_final',
            'listado_monitoreo.*.codigo_unidad_medida' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    if (!DB::table('mmr.t_unidad_medida')->where('codigo_unidad_medida', $value)->exists()) {
                        $fail("El código de unidad de medida no existe.");
                    }
                },
            ],
            'listado_monitoreo.*.codigo_tipo_indicador' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    if (!DB::table('mmr.tipo_indicador')->where('codigo_tipo_indicador', $value)->exists()) {
                        $fail("El código de tipo de indicador no existe.");
                    }
                },
            ],
            'listado_monitoreo.*.codigo_categorizacion' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    if (!DB::table('mmr.t_categorizacion')->where('codigo_categorizacion', $value)->exists()) {
                        $fail("El código de categorización no existe.");
                    }
                },
            ],
            'listado_monitoreo.*.medio_verificacion' => 'required|string|max:100',
            'listado_monitoreo.*.fuente_financiamiento' => 'required|string|max:100',
            'listado_monitoreo.*.meta_cantidad_anual' => 'required|integer|min:1',
            'listado_monitoreo.*.codigo_tipo_riesgo' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    if (!DB::table('mmr.t_tipo_riesgo')->where('codigo_tipo_riesgo', $value)->exists()) {
                        $fail("El código de tipo de riesgo no existe.");
                    }
                },
            ],
            'listado_monitoreo.*.codigo_nivel_impacto' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    if (!DB::table('mmr.t_nivel_impacto')->where('codigo_nivel_impacto', $value)->exists()) {
                        $fail("El código de nivel de impacto no existe.");
                    }
                },
            ],
            'listado_monitoreo.*.descripcion_riesgo' => 'nullable|string',
            'listado_monitoreo.*.lista_meses' => 'required|string',
            'listado_monitoreo.*.lista_cantidades' => 'required|string',
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
