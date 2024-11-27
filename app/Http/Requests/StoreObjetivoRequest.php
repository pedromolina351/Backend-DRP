<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreObjetivoRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'objetivo_an_ods' => 'required|string'
        ];
    }

    public function messages(): array
    {
        return [
            'objetivo_an_ods.required' => 'El objetivo es obligatorio',
            'objetivo_an_ods.string' => 'El objetivo debe ser una cadena de texto'
        ];
    }

    
}
