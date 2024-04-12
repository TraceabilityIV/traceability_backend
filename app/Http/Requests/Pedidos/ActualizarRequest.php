<?php

namespace App\Http\Requests\Pedidos;

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
            'direccion_id' => 'exists:direcciones,id',
            'metodo_pago' => 'string|max:20',
            'tipo_pago' => 'enum:parcial,total',
        ];
    }
}
