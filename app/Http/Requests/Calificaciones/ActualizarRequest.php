<?php

namespace App\Http\Requests\Calificaciones;

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
            'calificacion' => 'numeric',
            'comentario' => 'string',
            'descripcion' => 'string',
        ];
    }
}
