<?php

namespace App\Portal\Http\Requests\V1\Employee;

use App\Http\Requests\ApiRequest;
use Illuminate\Validation\Rule;

/**
 * Class GenerateOfferContractRequest
 *
 * @package App\Portal\Http\Requests\V1\Employee
 */
class GenerateOfferContractRequest extends ApiRequest
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
        $user = $this->request->get('user');
        $rules = [
            'user.salutation' => 'string',
            'user.street' => 'string',
            'user.city_id' => 'numeric',
            'user.phone' => 'string',
            'user.email' => 'email',
            'user.employee_number' => 'string',
        ];

        if (isset($user['city_id'])) {
            $rules['user.postal_code'] = [
                'string',
                Rule::exists('postal_codes', 'code')->where(function ($query) use ($user) {
                    $query->where('city_id', $user['city_id']);
                })
            ];
        }
        return $rules;
    }
}
