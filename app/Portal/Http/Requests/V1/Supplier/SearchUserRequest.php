<?php

namespace App\Portal\Http\Requests\V1\Supplier;

use App\Http\Requests\ApiRequest;
use App\Portal\Gates\Company;
use App\Portal\Helpers\AuthHelper;
use App\Portal\Models\Role;
use App\Portal\Rules\CompanyAssociated;
use App\Portal\Rules\HasRole;
use App\Portal\Rules\UserExists;

class SearchUserRequest extends ApiRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => 'required_without:code|email',
            'code' => 'required_without:email|string',
        ];
    }
}
