<?php

namespace App\System\Http\Requests;

use App\Http\Requests\ApiRequest;
use App\System\Models\User;
use Illuminate\Validation\Rule;

/**
 * Class CreateUserRequest
 *
 * @package App\System\Http\Requests
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
            'first_name'        => 'required|string',
            'last_name'         => 'required|string',
            'email'             => 'required|email|unique:users,email,NULL,id,deleted_at,NULL',
            'organization_name' => 'nullable|string',
            'address'           => 'nullable|string',
            'status_id'            => ['required', Rule::in([User::STATUS_ACTIVE, User::STATUS_INACTIVE])],
        ];
    }
}
