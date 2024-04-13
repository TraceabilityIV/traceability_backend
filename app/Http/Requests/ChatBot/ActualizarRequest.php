<?php

namespace App\Http\Requests\ChatBot;

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
            'mensaje' => 'min:3|max:100',
            'descripcion' => 'max:100',
            'accion' => 'in:finalizar,continuar,iniciar',
        ];
    }
}
