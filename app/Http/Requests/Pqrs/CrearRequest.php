<?php

namespace App\Http\Requests\Pqrs;

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
            'nombres' => 'required|string|min:3',
            'correo' => 'required|email',
            'telefono' => 'required|numeric|regex:/^(1-)?\d{10}$/',
            'direccion' => 'required|string|min:3',
            'asunto' => 'required|string|min:3',
            'descripcion' => 'required|string|min:3',
            'usuario_id' => 'exists:users,id',
            'barrio_id' => 'required|exists:barrios,id',
        ];
    }
}
