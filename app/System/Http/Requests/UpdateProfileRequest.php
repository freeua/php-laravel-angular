<?php

namespace App\System\Http\Requests;

use App\Http\Requests\ApiRequest;
use App\Rules\NewPassword;
use App\Rules\StrongPassword;
use Illuminate\Support\Facades\Auth;

/**
 * Class UpdateProfileRequest
 *
 * @package App\System\Http\Requests
 */
class UpdateProfileRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *Inaktiv
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
            'organization_name' => 'nullable|string',
            'address'           => 'nullable|string',
            'old_password'      => 'required_with:password|string|old_password:' . Auth::user()->password,
            'password'          => [
                'required_with:old_password',
                'confirmed',
                new StrongPassword(),
                new NewPassword(),
                'not_contains:' . $this->input('first_name') . ',' . $this->input('last_name')
            ],
        ];
    }
}
