<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CorreoCambioRequest extends FormRequest
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
            'correo_usuario' => 'required',
            'accion' => 'required|integer|in:1,2,3',
            'password_string' => 'nullable'
        ];
    }


}
