<?php

namespace App\Portal\Http\Requests\V1;

use App\Http\Requests\ApiRequest;
use App\Portal\Models\User;
use App\Portal\Models\Role;
use App\Rules\StrongPassword;
use Illuminate\Validation\Rule;

/**
 * Class CreateUserRequest
 *
 * @package App\Portal\Http\Requests\V1
 */
class CreateUserRequest extends ApiRequest
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
        return [
            'role'              => ['required', 'string', Rule::in([Role::ROLE_PORTAL_ADMIN, Role::ROLE_COMPANY_ADMIN])],
            'hasEditPermission' => 'required_if:role,' . Role::ROLE_PORTAL_ADMIN . '|boolean',
            'company_id'        => 'integer|required_if:role,' . Role::ROLE_COMPANY_ADMIN . '|exists:companies,id,deleted_at,NULL',
            'first_name'        => 'required|string',
            'last_name'         => 'required|string',
            'email'             => 'required|email|unique:portal_users,email,NULL,id,deleted_at,NULL',
            'password'          => ['required', new StrongPassword(), 'not_contains:' . $this->input('first_name') . ',' . $this->input('last_name')],
            'status_id'            => ['required', Rule::in([User::STATUS_ACTIVE, User::STATUS_INACTIVE])],
        ];
    }
}
