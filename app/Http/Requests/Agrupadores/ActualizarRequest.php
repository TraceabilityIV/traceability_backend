<?php

namespace App\Http\Requests\Agrupadores;

use App\Http\Requests\BaseRequest;

class ActualizarRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nombre' => 'string|min:3',
            'codigo' => 'string|min:3',
            'estado' => 'boolean'
        ];
    }
}
