<?php

namespace App\Http\Requests\Paises;

use App\Http\Requests\BaseRequest;

class CrearRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'nombre' => 'required|string|min:3',
            'indicador' => 'required|string|min:1',
            'nombre_corto' => 'required|string|min:1',
            'codigo_postal' => 'numeric',
            'estado' => 'boolean',
            'bandera' => 'file|max:4098',
        ];
    }
}
