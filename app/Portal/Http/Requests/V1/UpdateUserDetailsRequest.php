<?php

namespace App\Portal\Http\Requests\V1;

use App\Http\Requests\ApiRequest;
use App\Portal\Models\User;
use App\Portal\Models\Role;
use App\Rules\NewPassword;
use App\Rules\StrongPassword;
use Illuminate\Validation\Rule;

/**
 * Class UpdateUserDetailsRequest
 *
 * @package App\Portal\Http\Requests\V1
 */
class UpdateUserDetailsRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $role = $this->route('user')->getRoleName();

        $rules = [
            'status_id'                    => ['required', Rule::in([User::STATUS_ACTIVE, User::STATUS_INACTIVE])],
            'password'                  => [
                'string',
                'confirmed',
                new StrongPassword(),
                new NewPassword()
            ],
        ];


        if ($role === Role::ROLE_PORTAL_ADMIN) {
            $rules['hasEditPermission'] = 'required|boolean';
        }


        return $rules;
    }
}
