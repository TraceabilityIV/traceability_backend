<?php

namespace App\Http\Requests\Ciudades;

use App\Http\Requests\BaseRequest;

class CrearRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nombre' => 'required|string|min:3',
            'indicador' => 'string|min:1',
            'nombre_corto' => 'required|string|min:1',
            'codigo_postal' => 'numeric',
            'estado' => 'boolean',
            'bandera' => 'file|max:4098',
            'departamento_id' => 'required|exists:departamentos,id',
        ];
    }
}
