<?php

namespace App\Http\Requests\Usuarios;

use App\Http\Requests\BaseRequest;

class PerfilRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'email' => 'email',
            'password' => 'string|min:8',
            'nombres' => 'string|min:3',
            'apellidos' => 'string',
            'telefono' => 'integer|regex:/^(1-)?\d{10}$/',
            'avatar' => 'file|max:4098',
        ];
    }
}
