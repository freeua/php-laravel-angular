<?php
namespace App\Portals\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\LeasingSettings\RateResource;
use App\System\Http\Resources\PortalLeasingSettingResource;

class BasicPortalResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'partnerId' => $this->partnerId,
            'allowEmployeeOfferCreation' => $this->allowEmployeeOfferCreation,
            'automaticCreditNote' => $this->automaticCreditNote,
            'name' => $this->name,
            'logo' => $this->logo,
            'color' => $this->color,
            'status' => $this->status,
            'insuranceRates' => RateResource::collection($this->insuranceRates()->with('productCategory')->get()),
            'serviceRates' => RateResource::collection($this->serviceRates()->with('productCategory')->get()),
            'leasingConditions' => PortalLeasingSettingResource::collection($this->leasingConditions()->with('productCategory')->get()),
            'leasingablePdf' => $this->leasingable_pdf,
            'imprintPdf' => $this->imprintPdf,
            'imprint' => $this->imprint,
            'policyPdf' => $this->policyPdf,
            'policy' => $this->policy,
        ];
    }
}
