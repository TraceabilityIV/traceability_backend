<?php

namespace App\Http\Requests\Estados;

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
            'nombre' => 'required|min:3|max:100',
            'icono_cumplido' => 'file|max:4098',
            'icono' => 'file|max:4098',
            'estado' => 'boolean',
            'flag_inicial' => 'boolean',
            'flag_final' => 'boolean',
            'estado_siguiente_id' => 'exists:estados,id',
        ];
    }
}
