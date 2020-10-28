<?php

namespace App\Portal\Http\Requests\V1;

use App\Http\Requests\ApiRequest;
use App\Rules\NewPassword;
use App\Rules\StrongPassword;
use App\Portal\Helpers\AuthHelper;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends ApiRequest
{
    public function authorize()
    {
        return true;
    }

    public function baseRules()
    {
        return [
            'first_name' => 'required|string',
            'last_name'  => 'required|string',
            'salutation' => 'required|string',
            'phone' => 'required|string',
            'password'   => [
                'string',
                'confirmed',
                new StrongPassword(),
                new NewPassword(),
                'not_contains:' . $this->input('first_name') . ',' . $this->input('last_name')
            ],
            'policy_checked' => 'present|nullable|boolean'
        ];
    }

    public function employeeRules()
    {
        $city_id = $this->request->get('city_id');
        return array_merge($this->baseRules(), [
            'city_id' => 'required|integer',
            'street' => 'required|string',
            'country' => 'nullable|string',
            'postal_code' => [
                        'required',
                        'string',
                        Rule::exists('postal_codes', 'code')->where(function ($query) use ($city_id) {
                            $query->where('city_id', $city_id);
                        })
                    ],
            'employee_number' => 'required|string',
        ]);
    }

    public function supplierRules()
    {
        $city_id = $this->request->get('city_id');
        return array_merge($this->baseRules(), [
            'city_id' => 'required|integer',
            'street' => 'required|string',
            'country' => 'required|string',
            'postal_code' => [
                                'required',
                                'string',
                                Rule::exists('postal_codes', 'code')->where(function ($query) use ($city_id) {
                                    $query->where('city_id', $city_id);
                                })
                            ],
        ]);
    }
    public function companyAdminRules()
    {
        return array_merge($this->baseRules(), [
            'employee_number' => 'required|string',
        ]);
    }

    public function portalAdminRules()
    {
        $city_id = $this->request->get('city_id');
        return array_merge($this->baseRules(), [
            'city_id' => 'required|integer',
            'street' => 'required|string',
            'country' => 'required|string',
            'postal_code' => [
                'required',
                'string',
                Rule::exists('postal_codes', 'code')->where(function ($query) use ($city_id) {
                    $query->where('city_id', $city_id);
                })
            ],
            'employee_number' => 'required|string',
        ]);
    }

    public function rules()
    {
        $user = AuthHelper::user();
        if ($user->isEmployee()) {
            return $this->employeeRules();
        } elseif ($user->isSupplier()) {
            return $this->supplierRules();
        } elseif ($user->isCompanyAdmin()) {
            return $this->companyAdminRules();
        }
        return $this->portalAdminRules();
    }
}
