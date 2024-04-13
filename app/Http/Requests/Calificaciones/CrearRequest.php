<?php

namespace App\Http\Requests\Calificaciones;

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
            'pedido_id' => 'required|exists:pedidos,id',
            'calificacion' => 'required|numeric',
            'comentario' => 'string',
            'descripcion' => 'string',
        ];
    }
}
