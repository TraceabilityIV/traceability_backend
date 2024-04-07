<?php

namespace App\Http\Requests\Departamentos;

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
            'indicador' => 'string|min:1',
            'nombre_corto' => 'string|min:1',
            'codigo_postal' => 'numeric',
            'estado' => 'boolean',
            'bandera' => 'file|max:4098',
            'pais_id' => 'exists:paises,id'
        ];
    }
}
