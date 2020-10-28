<?php


namespace App\Partners\Requests;

use App\Http\Requests\ApiRequest;
use App\Portal\Helpers\AuthHelper;

class PartnerInfoRequest extends ApiRequest
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
