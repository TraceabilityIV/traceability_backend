<?php

namespace App\Http\Requests\Barrios;

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
            'nombre_corto' => 'string|min:1',
            'codigo_postal' => 'numeric',
            'estado' => 'boolean',
            'ciudad_id' => 'exists:ciudades,id',
        ];
    }
}
