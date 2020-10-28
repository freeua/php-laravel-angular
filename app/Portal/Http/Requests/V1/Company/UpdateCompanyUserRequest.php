<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 07.03.2019
 * Time: 13:44
 */

namespace App\Portal\Http\Requests\V1\Company;

use App\Http\Requests\ApiRequest;
use App\Portal\Models\User;
use Illuminate\Validation\Rule;

/**
 * Class UpdateCompanyUserRequest
 * @package App\Portal\Http\Requests\V1\Company
 */
class UpdateCompanyUserRequest extends ApiRequest
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
            'status.id' => ['required', 'integer', Rule::in([User::STATUS_ACTIVE, User::STATUS_INACTIVE])],
            'max_user_contracts' => 'required_if:individual_settings,1|integer|min:1|nullable',
            'max_user_amount' => 'required_if:individual_settings,1|numeric|between:500,99999999.99|nullable',
            'min_user_amount' => 'required_if:individual_settings,1|numeric|between:0,99999999.99|nullable',
            'individual_settings' => 'boolean|nullable',
            'insurance_rate_subsidy' => 'required_if:individual_settings,1|boolean',
            'insurance_rate_subsidy_type' => ['required_if:insurance_rate_subsidy,1', 'nullable', Rule::in([User::TYPE_FIXED, User::TYPE_PERCENTAGE])],
            'insurance_rate_subsidy_amount' => 'required_if:insurance_rate_subsidy,1|nullable|numeric|between:0,999999.99',
            'service_rate_subsidy' => 'required_if:individual_settings,1|boolean',
            'service_rate_subsidy_type' => ['required_if:service_rate_subsidy,1', 'nullable', Rule::in([User::TYPE_FIXED, User::TYPE_PERCENTAGE])],
            'service_rate_subsidy_amount' => 'required_if:service_rate_subsidy,1|nullable|numeric|between:0,999999.99',
            'leasing_rate_subsidy' => 'required_if:individual_settings,1|boolean',
            'leasing_rate_subsidy_type' => ['required_if:leasing_rate_subsidy,1', 'nullable', Rule::in([User::TYPE_FIXED, User::TYPE_PERCENTAGE])],
            'leasing_rate_subsidy_amount' => 'required_if:leasing_rate_subsidy,1|nullable|numeric|between:0,999999.99',
            'is_accept_offer' => 'required|boolean'
        ];
    }
}
