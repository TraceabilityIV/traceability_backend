<?php

namespace App\Http\Requests\Menu;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class EditarRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "nombre" => "string|min:3",
            "id_referencia" => "string|min:1|unique:menus,id_referencia,{$this->route('menu')}",
            "icono" => "string",
            "color" => "string",
            "tipo_icono" => "string|in:imagen,clase",
            "estado" => "boolean",
            "permiso_id" => "exists:permisos,id",
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();
        $errorMessage = array_shift($errors)[0] ?? '';
        throw new HttpResponseException(
            response()->json([
                'error' => "Parametros Incorrectos",
                'mensaje' => $errorMessage
            ], 422)
        );
    }
}
