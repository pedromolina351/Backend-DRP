<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
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
            'codigo_usuario' => 'required|integer',
            'comentario_texto' => 'required|string',
            'codigo_comentario_padre' => 'integer|nullable',
            'codigo_poa' => 'required|integer'
        ];
    }

    public function messages(): array
    {
        return [
            'codigo_usuario.required' => 'El código del usuario es obligatorio.',
            'comentario_texto.required' => 'El texto del comentario es obligatorio.',
            'codigo_comentario_padre.integer' => 'El código del comentario padre debe ser un número entero.',
            'codigo_poa.required' => 'El código del poa es obligatorio.'
        ];
    }
}
