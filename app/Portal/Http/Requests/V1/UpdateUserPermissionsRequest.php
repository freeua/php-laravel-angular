<?php

namespace App\Portal\Http\Requests\V1;

use App\Http\Requests\ApiRequest;
use App\Portal\Helpers\AuthHelper;
use App\Portal\Models\Role;

/**
 * Class UpdateUserRequest
 *
 * @package App\Portal\Http\Requests\V1
 */
class UpdateUserPermissionsRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return AuthHelper::user()->hasRole(Role::ROLE_PORTAL_ADMIN) || AuthHelper::user()->hasRole(Role::ROLE_COMPANY_ADMIN) ;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'permissions'           => 'present|array',
            'permissions.*.id'    => 'required|numeric',
        ];


        return $rules;
    }
}
