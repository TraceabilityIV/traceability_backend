<?php

namespace App\Http\Requests\Barrios;

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
            'nombre_corto' => 'required|string|min:1',
            'codigo_postal' => 'numeric',
            'estado' => 'boolean',
            'ciudad_id' => 'required|exists:ciudades,id',
        ];
    }
}
