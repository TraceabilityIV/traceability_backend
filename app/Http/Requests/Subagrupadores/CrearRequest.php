<?php

namespace App\Http\Requests\Subagrupadores;

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
            'codigo' => 'required|string|min:3|max:150',
            'estado' => 'boolean',
            'agrupador_id' => 'required|exists:agrupadores,id',
        ];
    }
}
