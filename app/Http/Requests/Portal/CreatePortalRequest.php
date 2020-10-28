<?php

namespace App\Http\Requests\Portal;

use App\Http\Requests\ApiRequest;
use App\Models\Portal;
use App\Models\Rates\ServiceRate;
use Illuminate\Validation\Rule;

/**
 * Class CreatePortalRequest
 *
 * @package App\System\Http\Requests
 */
class CreatePortalRequest extends ApiRequest
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
            'name' => 'required|string',
            'subdomain' => 'required|string|unique:portals,subdomain,NULL,id,deleted_at,NULL',
            'status.id' => ['required', Rule::in([Portal::STATUS_ACTIVE, Portal::STATUS_INACTIVE])],
            'adminFirstName' => 'required|string',
            'adminLastName' => 'required|string',
            'adminEmail' => 'required|email|unique:portals,admin_email,NULL,id,deleted_at,NULL',
            'companyName' => 'required|string',
            'companyCity.id' => 'required|integer|exists:cities,id',
            'companyAddress' => 'required|string',
            'companyVat' => 'required|string|max:15|unique:portals,company_vat,NULL,id,deleted_at,NULL',
            'leasingConditions.*.productCategory.id' => 'required|integer|distinct|exists:product_categories,id,deleted_at,NULL',
            'leasingConditions.*.factor' => 'required|numeric|between:0,99',
            'leasingConditions.*.period' => 'required|integer|between:1,60',
            'leasingConditions.*.residualValue' => 'present|nullable|numeric|between:0,99',
            'serviceRates.*.name' => 'required|string|max:100',
            'serviceRates.*.productCategory.id' => 'required|numeric',
            'serviceRates.*.amount' => 'required|numeric|between:0,100',
            'serviceRates.*.amountType' => ['required', Rule::in(['fixed', 'percentage'])],
            'serviceRates.*.type' => ['required', Rule::in([ServiceRate::INSPECTION, ServiceRate::FULL_SERVICE])],
            'serviceRates.*.minimum' => 'required|numeric|between:0,100000',
            'insuranceRates.*.name' => 'required|string|max:100',
            'insuranceRates.*.productCategory.id' => 'required|numeric',
            'insuranceRates.*.amount' => 'required|numeric|between:0,100',
            'insuranceRates.*.amountType' => ['required', Rule::in(['fixed', 'percentage'])],
            'insuranceRates.*.minimum' => 'required|numeric|between:0,100000',
            'imprint' => 'present|string',
            'policy' => 'present|string',
            'autoresponderText' => 'present|nullable|string',
            'automaticCreditNote' => 'required|boolean',
            'allowEmployeeOfferCreation' => 'required|boolean',
            'insuranceRateSubsidy' => 'required|boolean',
            'insuranceRateSubsidyType' => ['required', Rule::in(['fixed', 'percentage'])],
            'insuranceRateSubsidyAmount' => 'required|numeric|between:0,100',
            'serviceRateSubsidy' => 'required|boolean',
            'serviceRateSubsidyType' => ['required', Rule::in(['fixed', 'percentage'])],
            'serviceRateSubsidyAmount' => 'required|numeric|between:0,100',
            'leasingRateSubsidy' => 'required|boolean',
            'leasingRateSubsidyType' => ['required', Rule::in(['fixed', 'percentage'])],
            'leasingRateSubsidyAmount' => 'required|numeric|between:0,100',
        ];
    }
}
