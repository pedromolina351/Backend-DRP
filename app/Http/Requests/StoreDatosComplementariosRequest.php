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
            'GrupoEdadID' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    if (!DB::table('mmr.t_GruposEdad')->where('GrupoEdadID', $value)->exists()) {
                        $fail('El ID del grupo de edad no existe.');
                    }
                },
            ],
            'GeneroID' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    if (!DB::table('mmr.t_Generos')->where('GeneroID', $value)->exists()) {
                        $fail('El ID del género no existe.');
                    }
                },
            ],
            'CantidadBeneficiarios' => 'required|integer|min:1',
            'PuebloID' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    if (!DB::table('mmr.t_Pueblos')->where('PuebloID', $value)->exists()) {
                        $fail('El ID del pueblo no existe.');
                    }
                },
            ],
            'CantidadPueblo' => 'required|integer|min:1',
            'NombreUnidad' => 'nullable|string|max:100',
            'ResponsableUnidad' => 'nullable|string|max:100',
            'PresupuestoTotal' => 'nullable|numeric|min:0',
            'InversionMujeres' => 'nullable|numeric|min:0',
            'InversionFamilia' => 'nullable|numeric|min:0',
            'InversionIgualdad' => 'nullable|numeric|min:0',
            'CantidadTotalBeneficiarios' => 'nullable|integer|min:0',
        ];
    }

    public function messages()
    {
        return [
            'codigo_poa.required' => 'El código del POA es obligatorio.',
            'codigo_poa.integer' => 'El código del POA debe ser un número entero.',
            'codigo_poa.exists' => 'El código del POA no existe en la base de datos.',
            'GrupoEdadID.required' => 'El ID del grupo de edad es obligatorio.',
            'GrupoEdadID.exists' => 'El ID del grupo de edad no existe en la base de datos.',
            'GeneroID.required' => 'El ID del género es obligatorio.',
            'GeneroID.exists' => 'El ID del género no existe en la base de datos.',
            'CantidadBeneficiarios.required' => 'La cantidad de beneficiarios es obligatoria.',
            'PuebloID.required' => 'El ID del pueblo es obligatorio.',
            'PuebloID.exists' => 'El ID del pueblo no existe en la base de datos.',
            'CantidadPueblo.required' => 'La cantidad de beneficiarios del pueblo es obligatoria.',
            'PresupuestoTotal.numeric' => 'El presupuesto total debe ser un número válido.',
            'InversionMujeres.numeric' => 'La inversión en mujeres debe ser un número válido.',
            'InversionFamilia.numeric' => 'La inversión en familia debe ser un número válido.',
            'InversionIgualdad.numeric' => 'La inversión en igualdad debe ser un número válido.',
        ];
    }
}
