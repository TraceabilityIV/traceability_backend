<?php

namespace App\Http\Requests\Trazabilidad;

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
            'aplicacion' => 'string|min:3|max:255',
            'descripcion' => 'string|min:3',
            'resultados' => 'string|min:3',
            'ultima_revision' => 'date',
            'fecha_aplicacion' => 'date',
        ];
    }
}
