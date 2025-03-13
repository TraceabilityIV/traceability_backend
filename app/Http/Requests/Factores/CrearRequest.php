<?php

namespace App\Http\Requests\Factores;

use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;

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
			'nombre' => 'required|string|min:3',
			'descripcion' => 'string|min:3',
			'peso' => 'required|numeric',
			'fecha_inicio' => 'date',
			'fecha_fin' => 'date',
			'barrio_id' => 'exists:barrios,id',
			'ciudad_id' => 'required|exists:ciudades,id',
			'departamento_id' => 'exists:departamentos,id',
			'pais_id' => 'exists:paises,id',
			'latitud' => 'required|string',
			'longitud' => 'required|string',
			'radio' => 'numeric',
			'poligono' => 'string',
			'cultivos' => 'array',
			'cultivos.*' => 'exists:cultivos_predefinidos,id'
        ];
    }
}
