<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePantallaRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'codigo_modulo' => 'required|integer',
            'nombre_pantalla' => 'required|string|max:255',
            'url' => 'required|string|max:255',
            'estado' => 'boolean',
        ];
    }
}
