<?php

namespace App\Http\Requests\Evidencias;

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
            'descripcion' => 'string|min:3|max:255',
            'evidencia' => 'file|max:4098',
        ];
    }
}
