<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class StoreMonitoreoProductosIntermediosRequest extends FormRequest
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
            'listado_monitoreo.*.codigo_producto_intermedio' => 'required|integer|exists:t_productos_intermedios,codigo_producto_intermedio',
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
            'listado_monitoreo.*.lista_meses' => 'required|string', // Coma separada
            'listado_monitoreo.*.lista_cantidades' => 'required|string', // Coma separada
        ];
    }
    
    public function messages()
    {
        return [
            'codigo_poa.required' => 'El código del POA es obligatorio.',
            'codigo_poa.exists' => 'El código del POA no existe en la base de datos.',
            'listado_monitoreo.required' => 'Debe proporcionar al menos un producto intermedio.',
            'listado_monitoreo.*.codigo_producto_intermedio.required' => 'El código del producto intermedio es obligatorio.',
            'listado_monitoreo.*.codigo_producto_intermedio.exists' => 'El código del producto intermedio no existe en la base de datos.',
            'listado_monitoreo.*.nombre_unidad_organizativa.required' => 'El nombre de la unidad organizativa es obligatorio.',
            'listado_monitoreo.*.nombre_responsable_unidad_organizativa.required' => 'El nombre del responsable de la unidad organizativa es obligatorio.',
            'listado_monitoreo.*.medio_verificacion.required' => 'El medio de verificación es obligatorio.',
            'listado_monitoreo.*.meta_cantidad_anual.required' => 'La meta anual es obligatoria y debe ser mayor a 0.',
            'listado_monitoreo.*.lista_meses.required' => 'La lista de meses es obligatoria.',
            'listado_monitoreo.*.lista_cantidades.required' => 'La lista de cantidades es obligatoria.',
        ];
    }
    
}
