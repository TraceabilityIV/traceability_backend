<?php

namespace App\Http\Requests\Usuarios;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class RegistrarRequest extends FormRequest
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
            'email' => 'required|email|unique:users,email',
            'password' => 'string',
            'nombres' => 'required|string|min:3',
            'apellidos' => 'string',
            'telefono' => 'required|integer|regex:/^(1-)?\d{10}$/',
            'estado' => 'boolean',
            'avatar' => 'file|max:4098',
            'doc_identificacion' => 'file|max:4098',
            'rut' => 'file|max:4098',
            'contrato' => 'file|max:4098',
            'device_name' => 'string',
            'tipo_cliente' => 'in:Cliente,Vendedor'
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
