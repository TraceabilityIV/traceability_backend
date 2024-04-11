<?php

namespace App\Http\Requests\Precios;

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
            'estado' => 'boolean',
            'precio_venta' => 'required|decimal:0,20',
            'cultivo_id' => 'required|exists:cultivos,id',
            'tipo_id' => 'required|exists:subagrupadores,id',
        ];
    }
}
