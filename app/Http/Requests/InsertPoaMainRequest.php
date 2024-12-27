<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InsertPoaMainRequest extends FormRequest
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
            // Validaciones generales
            'codigo_institucion' => 'required|integer|exists:t_instituciones,codigo_institucion',
            'codigo_programa' => 'required|integer|exists:t_programas,codigo_programa',
            'codigo_usuario_creador' => 'required|integer|exists:config_t_usuarios,codigo_usuario',
    
            // Validaciones para listado_politicas
            'listado_politicas' => 'required|array|min:1',
            'listado_politicas.*.codigo_politica' => 'required|integer|exists:t_politicas_publicas,codigo_politica_publica',
    
            // Validaciones para listado_objetivos
            'listado_objetivos' => 'required|array|min:1',
            'listado_objetivos.*.codigo_objetivo_an_ods' => 'required|integer|exists:t_objetivos_an_ods,codigo_objetivo_an_ods',
            'listado_objetivos.*.codigo_meta_an_ods' => 'required|integer|exists:t_metas_an_ods,codigo_meta_an_ods',
            'listado_objetivos.*.codigo_indicador_an_ods' => 'required|integer|exists:t_indicadores_an_ods,codigo_indicador_an_ods',
    
            // Validaciones para listado_objetivos_vp
            'listado_objetivos_vp' => 'required|array|min:1',
            'listado_objetivos_vp.*.codigo_objetivo_vp' => 'required|integer|exists:t_objetivos_vision_pais,codigo_objetivo_vision_pais',
            'listado_objetivos_vp.*.codigo_meta_vp' => 'required|integer|exists:t_metas_vision_pais,codigo_meta_vision_pais',
    
            // Validaciones para listado_plan_estrategico
            'listado_plan_estrategico' => 'required|array|min:1',
            'listado_plan_estrategico.*.codigo_gabinete' => 'required|integer|exists:t_gabinetes,codigo_gabinete',
            'listado_plan_estrategico.*.codigo_eje_estrategico' => 'required|integer|exists:t_eje_estrategicos,codigo_eje_estrategico',
            'listado_plan_estrategico.*.codigo_objetivo_peg' => 'required|integer|exists:t_objetivos_peg,codigo_objetivo_peg',
            'listado_plan_estrategico.*.codigo_resultado_peg' => 'required|integer|exists:t_resultado_peg,codigo_resultado_peg',
            'listado_plan_estrategico.*.codigo_indicador_resultado_peg' => 'required|integer|exists:t_indicador_resultado_peg,codigo_indicador_indicador_resultado_peg',
        ];
    }
    
    public function messages()
    {
        return [
            'codigo_institucion.required' => 'El código de institución es obligatorio.',
            'codigo_programa.required' => 'El código de programa es obligatorio.',
            'codigo_usuario_creador.required' => 'El código del usuario creador es obligatorio.',
    
            'listado_politicas.required' => 'Debe proporcionar al menos una política.',
            'listado_politicas.*.codigo_politica.exists' => 'La política pública especificada no existe.',
    
            'listado_objetivos.required' => 'Debe proporcionar al menos un objetivo AN-ODS.',
            'listado_objetivos.*.codigo_objetivo_an_ods.exists' => 'El objetivo AN-ODS especificado no existe.',
            'listado_objetivos.*.codigo_meta_an_ods.exists' => 'La meta AN-ODS especificada no existe.',
            'listado_objetivos.*.codigo_indicador_an_ods.exists' => 'El indicador AN-ODS especificado no existe.',
    
            'listado_objetivos_vp.required' => 'Debe proporcionar al menos un objetivo de Visión País.',
            'listado_objetivos_vp.*.codigo_objetivo_vp.exists' => 'El objetivo de Visión País especificado no existe.',
            'listado_objetivos_vp.*.codigo_meta_vp.exists' => 'La meta de Visión País especificada no existe.',
    
            'listado_plan_estrategico.required' => 'Debe proporcionar al menos un elemento del plan estratégico.',
            'listado_plan_estrategico.*.codigo_gabinete.exists' => 'El gabinete especificado no existe.',
            'listado_plan_estrategico.*.codigo_eje_estrategico.exists' => 'El eje estratégico especificado no existe.',
            'listado_plan_estrategico.*.codigo_objetivo_peg.exists' => 'El objetivo PEG especificado no existe.',
            'listado_plan_estrategico.*.codigo_resultado_peg.exists' => 'El resultado PEG especificado no existe.',
            'listado_plan_estrategico.*.codigo_indicador_resultado_peg.exists' => 'El indicador del resultado PEG especificado no existe.',
        ];
    }
    
}
