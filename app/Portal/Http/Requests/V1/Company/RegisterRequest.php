<?php

namespace App\Portal\Http\Requests\V1\Company;

use App\Http\Requests\ApiRequest;
use App\Rules\StrongPassword;

/**
 * Class RegisterRequest
 *
 * @package App\Portal\Http\Requests\V1\Company
 */
class RegisterRequest extends ApiRequest
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
            'email'      => 'required|email|unique:portal_users,email,NULL,id,deleted_at,NULL',
            'first_name' => 'required|string',
            'last_name'  => 'required|string',
            'password'   => [
                'required',
                'string',
                'confirmed',
                new StrongPassword(),
                'not_contains:' . $this->input('first_name') . ',' . $this->input('last_name')
            ],
        ];
    }
}
