<?php

namespace App\Http\Resources\Portals;

use App\Http\Resources\LeasingSettings\RateResource;
use App\Models\Rates\InsuranceRate;
use App\Models\Portal;
use App\System\Http\Resources\PortalLeasingSettingResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

/**
 * Class PortalResource
 *
 * @package App\System\Http\Resources
 * @mixin Collection
 */
class PortalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        /** @var $this Portal */
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'logo' => $this->logo,
            'color' => $this->color,
            'mainDomain' => env('APP_URL_BASE'),
            'domain' => $this->domain,
            'subdomain' => $this->subdomain,
            'uuid' => $this->uuid,
            'adminFirstName' => $this->admin_first_name,
            'adminLastName' => $this->admin_last_name,
            'adminEmail' => $this->admin_email,
            'companyName' => $this->company_name,
            'companyCity' => $this->companyCity,
            'companyAddress' => $this->company_address,
            'companyVat' => $this->company_vat,
            'status' => $this->status,
            'insuranceRates' => RateResource::collection($this->insuranceRates()->with('productCategory')->get()),
            'serviceRates' => RateResource::collection($this->serviceRates()->with('productCategory')->get()),
            'leasingConditions' => PortalLeasingSettingResource::collection($this->leasingConditions()->with('productCategory')->get()),
            'leasingablePdf' => $this->leasingable_pdf,
            'servicePdf' => $this->servicePdf,
            'imprintPdf' => $this->imprintPdf,
            'imprint' => $this->imprint,
            'policyPdf' => $this->policyPdf,
            'policy' => $this->policy,
            'autoresponderText' => $this->autoresponderText,
            'automaticCreditNote' => $this->automaticCreditNote,
            'allowEmployeeOfferCreation' => $this->allowEmployeeOfferCreation,
            'insuranceRateSubsidy' => $this->insurance_rate_subsidy,
            'insuranceRateSubsidyType' => $this->insurance_rate_subsidy_type,
            'insuranceRateSubsidyAmount' => $this->insurance_rate_subsidy_amount,
            'serviceRateSubsidy' => $this->service_rate_subsidy,
            'serviceRateSubsidyType' => $this->service_rate_subsidy_type,
            'serviceRateSubsidyAmount' => $this->service_rate_subsidy_amount,
            'leasingRateSubsidy' => $this->leasing_rate_subsidy,
            'leasingRateSubsidyType' => $this->leasing_rate_subsidy_type,
            'leasingRateSubsidyAmount' => $this->leasing_rate_subsidy_amount,
        ];
    }
}
