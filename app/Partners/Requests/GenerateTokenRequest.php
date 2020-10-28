<?php


namespace App\Partners\Requests;

use App\Http\Requests\ApiRequest;
use App\Portal\Helpers\AuthHelper;

class GenerateTokenRequest extends ApiRequest
{
    public function authorize()
    {
        return AuthHelper::isEmployee();
    }

    public function rules()
    {
        return [];
    }
}
