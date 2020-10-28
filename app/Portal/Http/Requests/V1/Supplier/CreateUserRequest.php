<?php

namespace App\Portal\Http\Requests\V1\Supplier;

use App\Http\Requests\ApiRequest;
use App\Portal\Models\User;
use App\Rules\StrongPassword;
use Illuminate\Validation\Rule;

/**
 * Class CreateUserRequest
 *
 * @package App\Portal\Http\Requests\V1\Supplier
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
            'first_name' => 'required|string',
            'last_name'  => 'required|string',
            'email'      => 'required|email|unique:portal_users,email,NULL,id,deleted_at,NULL',
            'password'   => ['required', new StrongPassword(), 'not_contains:' . $this->input('first_name') . ',' . $this->input('last_name')],
        ];
    }
}
