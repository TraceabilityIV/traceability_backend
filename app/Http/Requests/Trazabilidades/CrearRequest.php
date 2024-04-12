<?php

namespace App\Http\Requests\Trazabilidades;

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
            'descripcion' => 'string|min:3',
            'observaciones' => 'string|min:3',
            'flag_entregado' => 'boolean',
            'pedido_id' => 'required|exists:pedidos,id',
        ];
    }
}
