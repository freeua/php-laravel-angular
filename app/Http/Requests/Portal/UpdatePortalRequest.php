<?php

namespace App\Http\Requests\Portal;

use App\Http\Requests\ApiRequest;
use App\Models\Portal;
use Illuminate\Validation\Rule;

/**
 * Class UpdatePortalRequest
 *
 * @package App\System\Http\Requests
 */
class UpdatePortalRequest extends ApiRequest
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
        $portalId = $this->route('portal')->id;

        return [
            'name' => 'required|string',
            'color' => 'required|string',
            'subdomain' => 'required|string|unique:portals,subdomain,' . $portalId . ',id,deleted_at,NULL',
            'status.id' => ['required', Rule::in([Portal::STATUS_ACTIVE, Portal::STATUS_INACTIVE])],
            'adminFirstName' => 'required|string',
            'adminLastName' => 'required|string',
            'adminEmail' => 'required|email|unique:portals,admin_email,' . $portalId . ',id,deleted_at,NULL',
            'companyName' => 'required|string',
            'companyCity.id' => 'required|integer|exists:cities,id',
            'companyAddress' => 'required|string',
            'companyVat' => 'required|string|max:15|unique:portals,company_vat,' . $portalId . ',id,deleted_at,NULL',
            'imprint' => 'present|nullable|string',
            'policy' => 'present|nullable|string',
            'autoresponderText' => 'present|nullable|string',
            'allowEmployeeOfferCreation' => 'required|boolean',
            'automaticCreditNote' => 'required|boolean',
            'insuranceRateSubsidy' => 'required|boolean',
            'insuranceRateSubsidyType' => ['required', Rule::in(['fixed', 'percentage'])],
            'insuranceRateSubsidyAmount' => 'required|numeric|between:0,100',
            'serviceRateSubsidy' => 'required|boolean',
            'serviceRateSubsidyType' => ['required', Rule::in(['fixed', 'percentage'])],
            'serviceRateSubsidyAmount' => 'required|numeric|between:0,100',
            'leasingRateSubsidy' => 'required|boolean',
            'leasingRateSubsidyType' => ['required', Rule::in(['fixed', 'percentage'])],
            'leasingRateSubsidyAmount' => 'required|numeric|between:0,100',
            'leasingablePdf' => 'nullable|string',
            'servicePdf' => 'nullable|string',
            'policyPdf' => 'nullable|string',
            'imprintPdf' => 'nullable|string',
            'logo' => 'nullable|string',
        ];
    }
}
