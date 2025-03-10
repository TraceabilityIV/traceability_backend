<?php

namespace App\Http\Requests\Cultivos;

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
            // 'nombre' => 'required|string|min:3|max:255',
            'estado' => 'boolean',
            'ubicacion' => 'string|min:3|max:100',
            'direccion' => 'string|min:3|max:150',
            'latitud' => 'string|min:3|max:50',
            'longitud' => 'string|min:3|max:50',
            'fecha_siembra' => 'date',
            'area' => 'decimal:0,11',
            'variedad' => 'string|min:3|max:100',
            'lote' => 'string|max:20',
            'prefijo_registro' => 'string|max:20',
            'fecha_cosecha' => 'date',
            'cantidad_aproximada' => 'decimal:0,20',
            'usuario_id' => 'required|exists:users,id',
            'cultivo_predefinido_id' => 'required|exists:App\Models\CultivosPredefinidos,id'
        ];
    }
}
