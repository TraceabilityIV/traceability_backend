<?php

namespace App\Http\Requests\MensajesChat;

use App\Http\Requests\BaseRequest;

class IndexRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'usuario_envia_id' => 'required|exists:users,id',
            'usuario_recibe_id' => 'required|exists:users,id',
        ];
    }
}
