<?php

namespace App\Http\Requests\ChatBot;

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
            'mensaje' => 'required|min:3|max:100',
            'descripcion' => 'required|max:100',
            'accion' => 'required|in:finalizar,continuar,iniciar',
        ];
    }
}
