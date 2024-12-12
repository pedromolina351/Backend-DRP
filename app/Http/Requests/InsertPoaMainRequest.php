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
    public function rules(): array
    {
        return [
            'codigo_institucion' => 'required|integer',
            'codigo_programa' => 'required|integer',
            'codigo_usuario_creador' => 'required|integer',
            'codigo_politica' => 'required|integer',
            'codigo_objetivo_an_ods' => 'required|integer',
            'codigo_meta_an_ods' => 'required|integer',
            'codigo_indicador_an_ods' => 'required|integer',
            'codigo_objetivo_vp' => 'required|integer',
            'codigo_meta_vp' => 'required|integer',
            'codigo_gabinete' => 'required|integer',
            'codigo_eje_estrategico' => 'required|integer',
            'codigo_objetivo_peg' => 'required|integer',
            'codigo_resultado_peg' => 'required|integer',
            'codigo_indicador_resultado_peg' => 'required|integer'
        ];
    }

    public function messages(): array
    {
        return [
            'codigo_institucion.required' => 'El código de la institución es obligatorio.',
            'codigo_programa.required' => 'El código del programa es obligatorio.',
            'codigo_usuario_creador.required' => 'El código del usuario creador es obligatorio.',
            'codigo_politica.required' => 'El código de la política es obligatorio.',
            'codigo_objetivo_an_ods.required' => 'El código del objetivo an ods es obligatorio.',
            'codigo_meta_an_ods.required' => 'El código de la meta an ods es obligatorio.',
            'codigo_indicador_an_ods.required' => 'El código del indicador an ods es obligatorio.',
            'codigo_objetivo_vp.required' => 'El código del objetivo vp es obligatorio.',
            'codigo_meta_vp.required' => 'El código de la meta vp es obligatorio.',
            'codigo_gabinete.required' => 'El código del gabinete es obligatorio.',
            'codigo_eje_estrategico.required' => 'El código del eje estratégico es obligatorio.',
            'codigo_objetivo_peg.required' => 'El código del objetivo peg es obligatorio.',
            'codigo_resultado_peg.required' => 'El código del resultado peg es obligatorio.',
            'codigo_indicador_resultado_peg.required' => 'El código del indicador resultado peg es obligatorio.'
        ];
    }
}
