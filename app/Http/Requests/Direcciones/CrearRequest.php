<?php

namespace App\Http\Requests\Direcciones;

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
            'direccion' => 'required|string|min:3',
            'receptor' => 'string|min:3',
            'latitud' => 'string',
            'longitud' => 'string',
            'barrio_id' => 'required|exists:barrios,id',
            'usuario_id' => 'required|exists:users,id',
            'estado' => 'boolean',
        ];
    }
}
