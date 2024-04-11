<?php

namespace App\Http\Requests\Trazabilidad;

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
            'aplicacion' => 'required|string|min:3|max:255',
            'descripcion' => 'required|string|min:3',
            'resultados' => 'string|min:3',
            'cultivo_id' => 'required|exists:cultivos,id',
        ];
    }
}
