<?php

namespace App\Http\Requests\CostosEnvios;

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
            'costo' => 'decimal|min:0',
            'estado' => 'boolean',
            'tipo_costo_id' => 'exists:subagrupadores,id',
            'categorias' => 'array|exists:categorias,id',
        ];
    }
}
