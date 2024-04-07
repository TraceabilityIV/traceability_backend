<?php

namespace App\Http\Requests\Direcciones;

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
            'direccion' => 'string|min:3',
            'receptor' => 'string|min:3',
            'latitud' => 'string',
            'longitud' => 'string',
            'barrio_id' => 'exists:barrios,id',
            'usuario_id' => 'exists:users,id',
            'estado' => 'boolean',
        ];
    }
}
