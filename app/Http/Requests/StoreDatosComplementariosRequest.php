<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class StoreDatosComplementariosRequest extends FormRequest
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
            'NombreUnidad' => 'nullable|string|max:100',
            'ResponsableUnidad' => 'nullable|string|max:100',
            'PresupuestoTotal' => 'nullable|numeric|min:0',
            'InversionMujeres' => 'nullable|numeric|min:0',
            'InversionFamilia' => 'nullable|numeric|min:0',
            'InversionIgualdad' => 'nullable|numeric|min:0',
            'CantidadTotalBeneficiarios' => 'nullable|integer|min:0',
    
            'Listado_Beneficiarios_Programa' => 'required|array|min:1',
            'Listado_Beneficiarios_Programa.*.GrupoEdadID' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    if (!DB::table('mmr.t_GruposEdad')->where('GrupoEdadID', $value)->exists()) {
                        $fail('El ID del grupo de edad no existe.');
                    }
                },
            ],
            'Listado_Beneficiarios_Programa.*.GeneroID' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    if (!DB::table('mmr.t_Generos')->where('GeneroID', $value)->exists()) {
                        $fail('El ID del género no existe.');
                    }
                },
            ],
            'Listado_Beneficiarios_Programa.*.CantidadBeneficiarios' => 'required|integer|min:1',
    
            'Listado_Beneficiarios_Pueblos' => 'required|array|min:1',
            'Listado_Beneficiarios_Pueblos.*.PuebloID' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    if (!DB::table('mmr.t_Pueblos')->where('PuebloID', $value)->exists()) {
                        $fail('El ID del pueblo no existe.');
                    }
                },
            ],
            'Listado_Beneficiarios_Pueblos.*.CantidadPueblo' => 'required|integer|min:1',
        ];
    }
    
    public function messages()
    {
        return [
            'codigo_poa.required' => 'El código del POA es obligatorio.',
            'codigo_poa.integer' => 'El código del POA debe ser un número entero.',
            'codigo_poa.exists' => 'El código del POA no existe en la base de datos.',
    
            'NombreUnidad.string' => 'El nombre de la unidad debe ser una cadena de texto.',
            'NombreUnidad.max' => 'El nombre de la unidad no debe exceder los 100 caracteres.',
            'ResponsableUnidad.string' => 'El responsable de la unidad debe ser una cadena de texto.',
            'ResponsableUnidad.max' => 'El responsable de la unidad no debe exceder los 100 caracteres.',
            'PresupuestoTotal.numeric' => 'El presupuesto total debe ser un número válido.',
            'InversionMujeres.numeric' => 'La inversión en mujeres debe ser un número válido.',
            'InversionFamilia.numeric' => 'La inversión en familia debe ser un número válido.',
            'InversionIgualdad.numeric' => 'La inversión en igualdad debe ser un número válido.',
    
            'Listado_Beneficiarios_Programa.required' => 'El listado de beneficiarios por programa es obligatorio.',
            'Listado_Beneficiarios_Programa.array' => 'El listado de beneficiarios por programa debe ser un arreglo.',
            'Listado_Beneficiarios_Programa.*.GrupoEdadID.required' => 'El ID del grupo de edad es obligatorio.',
            'Listado_Beneficiarios_Programa.*.GrupoEdadID.exists' => 'El ID del grupo de edad no existe.',
            'Listado_Beneficiarios_Programa.*.GeneroID.required' => 'El ID del género es obligatorio.',
            'Listado_Beneficiarios_Programa.*.GeneroID.exists' => 'El ID del género no existe.',
            'Listado_Beneficiarios_Programa.*.CantidadBeneficiarios.required' => 'La cantidad de beneficiarios por programa es obligatoria.',
    
            'Listado_Beneficiarios_Pueblos.required' => 'El listado de beneficiarios por pueblos es obligatorio.',
            'Listado_Beneficiarios_Pueblos.array' => 'El listado de beneficiarios por pueblos debe ser un arreglo.',
            'Listado_Beneficiarios_Pueblos.*.PuebloID.required' => 'El ID del pueblo es obligatorio.',
            'Listado_Beneficiarios_Pueblos.*.PuebloID.exists' => 'El ID del pueblo no existe.',
            'Listado_Beneficiarios_Pueblos.*.CantidadPueblo.required' => 'La cantidad de beneficiarios del pueblo es obligatoria.',
        ];
    }
    
}
