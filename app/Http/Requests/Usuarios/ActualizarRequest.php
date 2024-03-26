<?php

namespace App\Http\Requests\Usuarios;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ActualizarRequest extends FormRequest
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
            'email' => [
                'email',
                Rule::unique('users', 'email')->ignore($this->route('usuario'))
            ],
            'password' => 'string|min:8',
            'nombres' => 'string|min:3',
            'apellidos' => 'string',
            'telefono' => 'integer|regex:/^(1-)?\d{10}$/',
            'estado' => 'boolean',
            'avatar' => 'file',
            'doc_identificacion' => 'file',
            'rut' => 'file',
            'contrato' => 'file',
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
