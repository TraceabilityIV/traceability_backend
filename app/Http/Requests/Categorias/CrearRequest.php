<?php

namespace App\Http\Requests\Categorias;

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
            'nombre' => 'required|string|min:3|max:100',
            'nombre_corto' => 'string|min:1|max:50',
            'imagen' => 'file|max:4098',
            'estado' => 'boolean',
        ];
    }
}
