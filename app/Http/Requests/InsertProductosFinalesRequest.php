<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InsertProductosFinalesRequest extends FormRequest
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
            'productos_finales' => 'required|array|min:1',
            'productos_finales.*.objetivo_operativo' => 'required|integer|exists:t_objetivos_operativos,codigo_objetivo_operativo',
            'productos_finales.*.producto_final' => 'required|string|max:500',
            'productos_finales.*.indicador_producto_final' => 'required|string|max:500',
            'productos_finales.*.producto_final_primario' => 'required|boolean',
            'productos_finales.*.programa' => 'required|string|max:500',
            'productos_finales.*.fecha_inicio' => 'string',
            'productos_finales.*.fecha_fin' => 'string',
            'productos_finales.*.responsable' => 'string',
            'productos_finales.*.medio_verificacion' => 'string',
            'productos_finales.*.subprograma' => 'required|string',
            'productos_finales.*.proyecto' => 'required|string',
            'productos_finales.*.actividad' => 'required|string',
            'productos_finales.*.costo_total_aproximado' => 'required|numeric|min:0',
            'productos_finales.*.nombre_obra' => 'required|string',
            'productos_finales.*.estado' => 'required|boolean',
        ];
    }

    public function messages()
    {
        return [
            'codigo_poa.required' => 'El campo codigo_poa es obligatorio.',
            'codigo_poa.integer' => 'El campo codigo_poa debe ser un número entero.',
            'codigo_poa.exists' => 'El codigo_poa proporcionado no existe.',
            'productos_finales.required' => 'Debe proporcionar al menos un producto final.',
            'productos_finales.array' => 'El campo productos_finales debe ser un arreglo.',
            'productos_finales.*.objetivo_operativo.required' => 'El campo objetivo_operativo es obligatorio.',
            'productos_finales.*.objetivo_operativo.integer' => 'El campo objetivo_operativo debe ser un número entero.',
            'productos_finales.*.objetivo_operativo.exists' => 'El objetivo operativo no existe.',
            'productos_finales.*.producto_final.required' => 'El campo producto_final es obligatorio.',
            'productos_finales.*.indicador_producto_final.required' => 'El campo indicador_producto_final es obligatorio.',
            'productos_finales.*.producto_final_primario.required' => 'El campo producto_final_primario es obligatorio.',
            'productos_finales.*.producto_final_primario.boolean' => 'El campo producto_final_primario debe ser verdadero o falso.',
            'productos_finales.*.programa.required' => 'El campo programa es obligatorio.',
            'productos_finales.*.subprograma.required' => 'El campo subprograma es obligatorio.',
            'productos_finales.*.proyecto.required' => 'El campo proyecto es obligatorio.',
            'productos_finales.*.actividad.required' => 'El campo actividad es obligatorio.',
            'productos_finales.*.costo_total_aproximado.required' => 'El campo costo_total_aproximado es obligatorio.',
            'productos_finales.*.costo_total_aproximado.numeric' => 'El campo costo_total_aproximado debe ser un número.',
            'productos_finales.*.nombre_obra.required' => 'El campo nombre_obra es obligatorio.',
            'productos_finales.*.estado.required' => 'El campo estado es obligatorio.',
            'productos_finales.*.estado.boolean' => 'El campo estado debe ser verdadero o falso.',
        ];
    }
}
