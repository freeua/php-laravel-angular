<?php

namespace App\Portal\Http\Requests\V1;

use App\Http\Requests\ApiRequest;
use App\Models\Companies\Company;
use Illuminate\Validation\Rule;

/**
 * Class UpdateCompanyRequest
 *
 * @package App\Portal\Http\Requests\V1
 */
class UpdateCompanyRequest extends ApiRequest
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
        $companyId = $this->route('company')->id;
        $city_id = $this->request->get('city_id');

        $rules = [
            'name' => 'required|string|unique:companies,name,' . $companyId . ',id,deleted_at,NULL',
            'vat' => 'required|string|max:15|unique:companies,vat,' . $companyId . ',id,deleted_at,NULL',
            'invoice_type' => ['required', Rule::in(Company::getInvoiceTypes())],
            'color' => 'present|string',
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
            'status.id' => ['required', 'integer', Rule::in(Company::getStatuses())],
            'max_user_contracts' => 'required|integer|min:1',
            'max_user_amount' => 'required|numeric|between:500,99999999.99',
            'min_user_amount' => 'required|numeric|between:0,99999999.99',
            'insurance_covered' => 'required|boolean',
            'insurance_covered_type' => ['required', Rule::in(Company::getServicePriceTypes())],
            'insurance_covered_amount' => 'required_if:insurance_covered,1|nullable|numeric|between:0,999999.99',
            'maintenance_covered' => 'required|boolean',
            'maintenance_covered_type' => ['required', Rule::in(Company::getServicePriceTypes())],
            'maintenance_covered_amount' => 'required_if:maintenance_covered,1|nullable|numeric|between:0,999999.99',
            'leasing_budget' => 'required|numeric|between:0,99999999.99',
            'leasing_rate' => 'required|boolean',
            'leasing_rate_type' => ['required', Rule::in(Company::getServicePriceTypes())],
            'leasing_rate_amount' => 'required_if:leasing_rate,1|nullable|numeric|between:0,999999.99',
            'logo' => 'present|nullable|string|max:3000000',
            'is_accept_employee' => 'required|boolean',
            'uses_default_subsidies' => 'required|boolean',
            's_pedelec_disable' => 'required|boolean',
            'pecuniary_advantage' => 'required|boolean',
            'gross_conversion' => ['required', Rule::in(Company::getGrossConversions())],
            'include_insurance_rate' => 'required|boolean',
            'include_service_rate' => 'required|boolean',
            'boni_number' => 'required|string',
            'gp_number' => 'required|string',
        ];

        if (request()->get('status')['id'] === Company::STATUS_ACTIVE) {
            $rules['end_contract'] = 'required|date|after:today';
        }

        return $rules;
    }
}
