<?php

namespace App\Http\Requests\Pedidos;

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
            'cultivo_id' => 'required|exists:cultivos,id',
            'direccion_id' => 'required|exists:direcciones,id',
            'total' => 'required|numeric',
            'subtotal' => 'required|numeric',
            'metodo_pago' => 'required|string|max:20',
            'tipo_pago' => 'required|in:parcial,total',
            'saldo' => 'required|numeric',
        ];
    }
}
