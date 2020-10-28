<?php
namespace App\ExternalLogin\Requests;

use App\Http\Requests\ApiRequest;

class LoginRequest extends ApiRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => 'string|required|email',
            'password' => 'string|required',
            'challenge' => 'string|required',
        ];
    }
}
