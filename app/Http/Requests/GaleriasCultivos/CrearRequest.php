<?php

namespace App\Http\Requests\GaleriasCultivos;

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
            'galeria' => 'file|max:4098',
            'galerias' => 'array|files|max:4098',
        ];
    }
}
