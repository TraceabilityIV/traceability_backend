<?php

namespace App\Http\Requests\Comisiones;

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
            'porcentaje' => 'required|decimal:0,2|min:0',
            'estado' => 'boolean',
            'categorias' => 'array|exists:categorias,id',
            'tipo_precios' => 'array|exists:subagrupadores,id'
        ];
    }
}
