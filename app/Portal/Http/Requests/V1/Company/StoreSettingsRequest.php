<?php

namespace App\Portal\Http\Requests\V1\Company;

use App\Http\Requests\ApiRequest;
use App\Models\Companies\Company;
use Illuminate\Validation\Rule;

/**
 * Class StoreSettingsRequest
 *
 * @package App\Portal\Http\Requests\V1\Company
 */
class StoreSettingsRequest extends ApiRequest
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
            'max_user_contracts' => 'required|integer|min:1',
            'max_user_amount' => 'required|numeric|between:500,99999999.99',
            'insurance_covered' => 'required|boolean',
            'insurance_covered_type' => ['required_if:insurance_covered,==,true', Rule::in(Company::getServicePriceTypes())],
            'insurance_covered_amount' => 'required_if:insurance_covered,==,true|numeric|between:0,999999.99',
            'maintenance_covered' => 'required|boolean',
            'maintenance_covered_type' => ['required_if:maintenance_covered,==,true', Rule::in(Company::getServicePriceTypes())],
            'maintenance_covered_amount' => 'required_if:maintenance_covered,==,true|numeric|between:0,999999.99',
            'leasing_rate' => 'required|boolean',
            'leasing_rate_type' => ['required_if:leasing_rate,==,true', Rule::in(Company::getServicePriceTypes())],
            'leasing_rate_amount' => 'required_if:leasing_rate,==,true|numeric|between:0,999999.99',
            'status_id' => ['required', Rule::in(Company::getStatuses())],
        ];
    }
}
