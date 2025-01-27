<?php

namespace App\Http\Requests;
use Illuminate\Support\Facades\DB;


use Illuminate\Foundation\Http\FormRequest;

class UpdateActividadRequest extends FormRequest
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
            'codigo_actividad_insumo' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    if (!DB::table('t_actividades_insumos')->where('codigo_actividad_insumo', $value)->exists()) {
                        $fail('El código de actividad-insumo especificado no existe.');
                    }
                },
            ],
            'codigo_producto_final' => 'nullable|integer|exists:t_productos_finales,codigo_producto_final',
            'actividad' => 'nullable|string|max:500',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'responsable' => 'nullable|string|max:255',
            'medio_verificacion' => 'nullable|string|max:255',
            'insumo_PACC' => 'nullable|string|max:255',
            'insumo_no_PACC' => 'nullable|string|max:255',
            'codigo_poa' => 'nullable|integer|exists:poa_t_poas,codigo_poa',
            'codigo_objetivo_operativo' => 'nullable|integer|exists:t_objetivos_operativos,codigo_objetivo_operativo',
        ];
    }

    /**
     * Mensajes personalizados para los errores de validación.
     */
    public function messages()
    {
        return [
            'codigo_actividad_insumo.required' => 'El código de actividad-insumo es obligatorio.',
            'codigo_actividad_insumo.integer' => 'El código de actividad-insumo debe ser un número entero.',
            'codigo_producto_final.integer' => 'El código del producto final debe ser un número entero.',
            'codigo_producto_final.exists' => 'El código del producto final especificado no existe.',
            'actividad.string' => 'La descripción de la actividad debe ser una cadena de texto.',
            'actividad.max' => 'La descripción de la actividad no puede exceder los 500 caracteres.',
            'fecha_inicio.date' => 'La fecha de inicio debe ser una fecha válida.',
            'fecha_fin.date' => 'La fecha de fin debe ser una fecha válida.',
            'fecha_fin.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.',
            'responsable.string' => 'El responsable debe ser una cadena de texto.',
            'responsable.max' => 'El responsable no puede exceder los 255 caracteres.',
            'medio_verificacion.string' => 'El medio de verificación debe ser una cadena de texto.',
            'medio_verificacion.max' => 'El medio de verificación no puede exceder los 255 caracteres.',
            'insumo_PACC.string' => 'El insumo PACC debe ser una cadena de texto.',
            'insumo_PACC.max' => 'El insumo PACC no puede exceder los 255 caracteres.',
            'insumo_no_PACC.string' => 'El insumo no PACC debe ser una cadena de texto.',
            'insumo_no_PACC.max' => 'El insumo no PACC no puede exceder los 255 caracteres.',
            'codigo_poa.integer' => 'El código POA debe ser un número entero.',
            'codigo_poa.exists' => 'El código POA especificado no existe.',
            'codigo_objetivo_operativo.integer' => 'El código del objetivo operativo debe ser un número entero.',
            'codigo_objetivo_operativo.exists' => 'El código del objetivo operativo especificado no existe.',
        ];
    }
}
