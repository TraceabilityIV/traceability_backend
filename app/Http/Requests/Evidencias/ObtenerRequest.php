<?php

namespace App\Http\Requests\Evidencias;

use App\Http\Requests\BaseRequest;

class ObtenerRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'trazabilidad_cultivos_id' => 'required|exists:trazabilidad_cultivos,id',
        ];
    }
}
