<?php

namespace App\Portal\Http\Requests\V1;

use App\Http\Requests\ApiRequest;
use App\Models\Companies\Company;
use App\Portal\Helpers\AuthHelper;
use Illuminate\Validation\Rule;

/**
 * Class UpdateCompanyRequest
 *
 * @package App\Portal\Http\Requests\V1
 */
class UpdateCompanyByAdminRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $company = $this->route('company');
        if (AuthHelper::isCompanyAdmin()) {
            return AuthHelper::companyId() === $company->id;
        } elseif (AuthHelper::isAdmin()) {
            return AuthHelper::user()->portal_id === $company->portal_id;
        }
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'status.id' => ['required', 'integer', Rule::in(Company::getStatuses())],
            'max_user_contracts' => 'required|integer|min:1',
            'max_user_amount' => 'required|numeric|between:500,99999999.99',
            'insurance_covered' => 'required|boolean',
            'insurance_covered_type' => ['required', Rule::in(Company::getServicePriceTypes())],
            'insurance_covered_amount' => 'required_if:insurance_covered,1|nullable|numeric|between:0,999999.99',
            'maintenance_covered' => 'required|boolean',
            'maintenance_covered_type' => ['required', Rule::in(Company::getServicePriceTypes())],
            'maintenance_covered_amount' => 'required_if:maintenance_covered,1|nullable|numeric|between:0,999999.99',
            'leasing_rate' => 'required|boolean',
            'leasing_rate_type' => ['required', Rule::in(Company::getServicePriceTypes())],
            'leasing_rate_amount' => 'required_if:leasing_rate,1|nullable|numeric|between:0,999999.99'
        ];
    }
}
