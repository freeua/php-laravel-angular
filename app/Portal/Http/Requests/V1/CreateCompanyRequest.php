<?php

namespace App\Portal\Http\Requests\V1;

use App\Http\Requests\ApiRequest;
use App\Models\Companies\Company;
use App\Models\Rates\ServiceRate;
use Illuminate\Validation\Rule;

/**
 * Class CreateCompanyRequest
 *
 * @package App\Portal\Http\Requests\V1
 */
class CreateCompanyRequest extends ApiRequest
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
        $city_id = $this->request->get('city_id');

        return [
            'color' => 'present|string',
            'name' => 'required|string|unique:companies,name,NULL,id,deleted_at,NULL',
            'slug' => 'required|alpha_dash|unique:companies,slug,NULL,id,deleted_at,NULL',
            'vat' => 'required|string|max:15|unique:companies,vat,NULL,id,deleted_at,NULL',
            'invoice_type' => ['required', Rule::in(Company::getInvoiceTypes())],
            'admin_first_name' => 'required|string',
            'admin_last_name' => 'required|string',
            'admin_email' => 'required|email|unique:companies,admin_email,NULL,id,deleted_at,NULL|unique:portal_users,email,NULL,id,deleted_at,NULL',
            'supplier_ids' => 'present|array',
            'supplier_ids.*' => 'nullable|integer',
            'subcompany_ids' => 'present|array',
            'subcompany_ids.*' => 'nullable|integer',
            'zip' => [
                        'required',
                        'string',
                        'max:20',
                        Rule::exists('postal_codes', 'code')->where(function ($query) use ($city_id) {
                            $query->where('city_id', $city_id);
                        })
                    ],
            'city_id' => 'required|integer|exists:cities,id',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'max_user_contracts' => 'required|integer|min:1',
            'max_user_amount' => 'required|numeric|between:500,99999999.99',
            'min_user_amount' => 'required|numeric|between:0,99999999.99',
            'insurance_covered' => 'required|boolean',
            'insurance_covered_type' => ['required', Rule::in(Company::getServicePriceTypes())],
            'insurance_covered_amount' => 'required_if:insurance_covered,==,true|numeric|between:0,999999.99',
            'maintenance_covered' => 'required|boolean',
            'maintenance_covered_type' => ['required', Rule::in(Company::getServicePriceTypes())],
            'maintenance_covered_amount' => 'required_if:maintenance_covered,==,true|numeric|between:0,999999.99',
            'leasing_budget' => 'required|numeric|between:0,99999999.99',
            'leasing_rate' => 'required|boolean',
            'leasing_rate_type' => ['required', Rule::in(Company::getServicePriceTypes())],
            'leasing_rate_amount' => 'required_if:leasing_rate,==,true|numeric|between:0,999999.99',
            'leasingConditions.*.productCategory.id' => 'required|integer|exists:product_categories,id,deleted_at,NULL',
            'leasingConditions.*.factor' => 'required|numeric|between:0,99',
            'leasingConditions.*.period' => 'required|integer|between:1,60',
            'leasingConditions.*.residualValue' => 'present|nullable|numeric|between:0,99',
            'leasingConditions.*.activeAt' => 'required|date',
            'leasingConditions.*.inactiveAt' => 'present|nullable|date',
            'serviceRates.*.name' => 'required|string|max:100',
            'serviceRates.*.productCategory.id' => 'required|numeric',
            'serviceRates.*.amount' => 'required|numeric|between:0,100000',
            'serviceRates.*.amountType' => ['required', Rule::in(['fixed', 'percentage'])],
            'serviceRates.*.type' => ['required', Rule::in([ServiceRate::INSPECTION, ServiceRate::FULL_SERVICE])],
            'serviceRates.*.minimum' => 'required|numeric|between:0,100000',
            'insuranceRates.*.name' => 'required|string|max:100',
            'insuranceRates.*.productCategory.id' => 'required|numeric',
            'insuranceRates.*.amount' => 'required|numeric|between:0,100000',
            'insuranceRates.*.amountType' => ['required', Rule::in(['fixed', 'percentage'])],
            'insuranceRates.*.minimum' => 'required|numeric|between:0,100000',
            'status.id' => ['required', Rule::in(Company::getStatuses())],
            'logo' => 'present|nullable|string|max:3000000',
            'is_accept_employee' => 'required|boolean',
            'uses_default_subsidies' => 'required|boolean',
            'include_insurance_rate' => 'required|boolean',
            'include_service_rate' => 'required|boolean',
            's_pedelec_disable' => 'required|boolean',
            'pecuniary_advantage' => 'required|boolean',
            'end_contract' => 'required|date|after:today',
            'gross_conversion' => ['required', Rule::in(Company::getGrossConversions())],
            'boni_number' => 'required|string',
            'gp_number' => 'required|string',
        ];
    }
}
