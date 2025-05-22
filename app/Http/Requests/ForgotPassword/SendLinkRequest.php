<?php

namespace App\Http\Requests\ForgotPassword;

use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class SendLinkRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|email',
        ];
    }
}
