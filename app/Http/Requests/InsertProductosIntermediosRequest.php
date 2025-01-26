<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InsertProductosIntermediosRequest extends FormRequest
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
            'productos_intermedios' => 'required|array|min:1',
            'productos_intermedios.*.objetivo_operativo' => 'required|integer|exists:t_objetivos_operativos,codigo_objetivo_operativo',
            'productos_intermedios.*.producto_intermedio' => 'required|string|max:500',
            'productos_intermedios.*.codigo_producto_final' => 'required|integer|exists:t_productos_finales,codigo_producto_final',
            'productos_intermedios.*.indicador_producto_intermedio' => 'required|string|max:500',
            'productos_intermedios.*.producto_intermedio_primario' => 'required|boolean',
            'productos_intermedios.*.programa' => 'required|string|max:500',
            'productos_intermedios.*.subprograma' => 'required|string|max:500',
            'productos_intermedios.*.proyecto' => 'required|string|max:500',
            'productos_intermedios.*.fecha_inicio' => 'string',
            'productos_intermedios.*.fecha_fin' => 'string',
            'productos_intermedios.*.responsable' => 'string',
            'productos_intermedios.*.medio_verificacion' => 'string',
            'productos_intermedios.*.actividad' => 'required|string|max:500',
            'productos_intermedios.*.fuente_financiamiento' => 'required|string|max:500',
            'productos_intermedios.*.ente_de_financiamiento' => 'required|string|max:500',
            'productos_intermedios.*.costro_aproximado' => 'required|numeric|min:0',
            'productos_intermedios.*.estado' => 'required|boolean',
         ];
     }
 
     public function messages()
     {
         return [
            'codigo_poa.required' => 'El campo codigo_poa es obligatorio.',
            'codigo_poa.exists' => 'El codigo_poa proporcionado no existe.',
            'productos_intermedios.required' => 'Debe proporcionar al menos un producto intermedio.',
            'productos_intermedios.*.objetivo_operativo.required' => 'El objetivo operativo es obligatorio.',
            'productos_intermedios.*.objetivo_operativo.exists' => 'El objetivo operativo no existe.',
            'productos_intermedios.*.codigo_producto_final.exists' => 'El producto final no existe.',
            'productos_intermedios.*.costro_aproximado.numeric' => 'El costo aproximado debe ser un número.',
            'productos_intermedios.*.estado.boolean' => 'El estado debe ser 0 o 1.',
         ];
     }
}
