<?php

namespace App\Http\Requests\Precios;

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
            'estado' => 'boolean',
            'precio_venta' => 'decimal:0,20',
            'tipo_id' => 'exists:subagrupadores,id',
        ];
    }
}
