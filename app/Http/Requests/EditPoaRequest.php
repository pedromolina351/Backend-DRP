<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class EditPoaRequest extends FormRequest
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
            // 'codigo_usuario_modificador' => [
            //     'required',
            //     'integer',
            //     function ($attribute, $value, $fail) {
            //         // Validación de existencia en la base de datos
            //         $user = DB::table('config_t_usuarios')
            //             ->where('codigo_usuario', $value)
            //             ->select('super_user', 'usuario_drp')
            //             ->first();

            //         if (!$user) {
            //             $fail('El código de usuario modificador es inválido.');
            //         } elseif ($user->super_user == 0 && $user->usuario_drp == 0) {
            //             $fail('El usuario no tiene permisos para realizar esta operación.');
            //         }
            //     },
            // ],
            'codigo_poa' => 'required|integer|exists:poa_t_poas,codigo_poa',
            'codigo_institucion' => 'nullable|integer|exists:t_instituciones,codigo_institucion',
            'codigo_programa' => 'nullable|integer|exists:t_programas,codigo_programa',
            'codigo_usuario_modificador' => 'required|integer|exists:config_t_usuarios,codigo_usuario',
            'codigo_politica' => 'nullable|integer|exists:t_politicas_publicas,codigo_politica_publica',
            'codigo_objetivo_an_ods' => 'nullable|integer|exists:t_objetivos_an_ods,codigo_objetivo_an_ods',
            'codigo_meta_an_ods' => 'nullable|integer|exists:t_metas_an_ods,codigo_meta_an_ods',
            'codigo_indicador_an_ods' => 'nullable|integer|exists:t_indicadores_an_ods,codigo_indicador_an_ods',
            'codigo_objetivo_vp' => 'nullable|integer|exists:t_objetivos_vision_pais,codigo_objetivo_vision_pais',
            'codigo_meta_vp' => 'nullable|integer|exists:t_metas_vision_pais,codigo_meta_vision_pais',
            'codigo_gabinete' => 'nullable|integer|exists:t_gabinetes,codigo_gabinete',
            'codigo_eje_estrategico' => 'nullable|integer|exists:t_eje_estrategicos,codigo_eje_estrategico',
            'codigo_objetivo_peg' => 'nullable|integer|exists:t_objetivos_peg,codigo_objetivo_peg',
            'codigo_resultado_peg' => 'nullable|integer|exists:t_resultado_peg,codigo_resultado_peg',
            'codigo_indicador_resultado_peg' => 'nullable|integer|exists:t_indicador_resultado_peg,codigo_indicador_indicador_resultado_peg',
        ];
    }

    public function messages()
    {
        return [
            'codigo_poa.required' => 'El campo codigo_poa es obligatorio.',
            'codigo_poa.exists' => 'El codigo_poa no existe.',
            'codigo_usuario_modificador.required' => 'El código del usuario modificador es obligatorio.',
            'codigo_usuario_modificador.exists' => 'El usuario modificador no tiene permisos válidos.',
            'codigo_institucion.exists' => 'La institución especificada no existe.',
            'codigo_programa.exists' => 'El programa especificado no existe.',
            'codigo_politica.exists' => 'La política especificada no existe.',
            'codigo_objetivo_an_ods.exists' => 'El objetivo de la Agenda 2030 especificado no existe.',
            'codigo_meta_an_ods.exists' => 'La meta de la Agenda 2030 especificada no existe.',
            'codigo_indicador_an_ods.exists' => 'El indicador de la Agenda 2030 especificado no existe.',
            'codigo_objetivo_vp.exists' => 'El objetivo de la Visión País especificado no existe.',
            'codigo_meta_vp.exists' => 'La meta de la Visión País especificada no existe.',
            'codigo_gabinete.exists' => 'El gabinete especificado no existe.',
            'codigo_eje_estrategico.exists' => 'El eje estratégico especificado no existe.',
            'codigo_objetivo_peg.exists' => 'El objetivo PEG especificado no existe.',
            'codigo_resultado_peg.exists' => 'El resultado PEG especificado no existe.',
            'codigo_indicador_resultado_peg.exists' => 'El indicador de resultado PEG especificado no existe.',
        ];
    }
}
