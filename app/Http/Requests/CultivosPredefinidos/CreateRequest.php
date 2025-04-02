<?php

namespace App\Http\Requests\CultivosPredefinidos;

use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
			'nombre' => 'required|string|min:3|max:255',
			'nombre_corto' => 'string',
			'categoria_id' => 'required|exists:App\Models\Categoria,id',
			'temperatura_min' => 'numeric',
			'temperatura_max' => 'numeric',
			'ph_min' => 'numeric',
			'ph_max' => 'numeric',
			'dias_crecimiento' => 'numeric',
			'profundidad_suelo' => 'numeric',
			'textura_suelo' => 'string|in:arcilloso,arenoso,franco-arenoso,franco-arcilloso,franco,limoso',
        ];
    }
}
