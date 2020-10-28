<?php

namespace App\Portal\Http\Resources\V1;

use App\Http\Resources\LeasingSettings\RateResource;
use App\Models\Companies\Company;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

/**
 * Class CompanyResource
 *
 * @package App\Portal\Http\Resources\V1
 * @mixin Collection
 */
class CompanyResource extends JsonResource
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
        /** @var $this Company */
        return [
            'id' => $this->id,
            'color' => $this->color,
            'logo' => $this->logo,
            'code' => $this->code,
            'name' => $this->name,
            'slug' => $this->slug,
            'vat' => $this->vat,
            'invoice_type' => $this->invoice_type,
            'admin_first_name' => $this->admin_first_name,
            'admin_last_name' => $this->admin_last_name,
            'admin_email' => $this->admin_email,
            'zip' => $this->zip,
            'city_id' => $this->city_id,
            'city' => new CityResource($this->city),
            'address' => $this->address,
            'phone' => $this->phone,
            'max_user_contracts' => $this->max_user_contracts,
            'max_user_amount' => $this->max_user_amount,
            'min_user_amount' => $this->min_user_amount,
            'insurance_covered' => $this->insurance_covered,
            'insurance_covered_type' => $this->insurance_covered_type,
            'insurance_covered_amount' => $this->insurance_covered_amount,
            'maintenance_covered' => $this->maintenance_covered,
            'maintenance_covered_type' => $this->maintenance_covered_type,
            'maintenance_covered_amount' => $this->maintenance_covered_amount,
            'leasing_budget' => $this->leasing_budget,
            'leasing_rate' => $this->leasing_rate,
            'leasing_rate_type' => $this->leasing_rate_type,
            'leasing_rate_amount' => $this->leasing_rate_amount,
            'status' => $this->status,
            'supplier_ids' => $this->suppliers->pluck('id'),
            'subcompany_ids' => $this->subcompanies->pluck('id'),
            'admins' => CompanyAdminResource::collection($this->admins),
            'leasingConditions' => CompanyLeasingSettingResource::collection($this->leasingConditions),
            'insuranceRates' => RateResource::collection($this->insuranceRates()->with('productCategory')->get()),
            'serviceRates' => RateResource::collection($this->serviceRates()->with('productCategory')->get()),
            'manual_contract_approval' => $this->manual_contract_approval,
            'is_accept_employee' => $this->is_accept_employee,
            'uses_default_subsidies' => $this->uses_default_subsidies,
            's_pedelec_disable' => $this->s_pedelec_disable,
            'pecuniary_advantage' => $this->pecuniary_advantage,
            'end_contract' => $this->end_contract,
            'gross_conversion' => $this->gross_conversion,
            'include_insurance_rate' => $this->include_insurance_rate,
            'include_service_rate' => $this->include_service_rate,
            'boni_number' => $this->boni_number,
            'gp_number' => $this->gp_number,
            'parent_id' => $this->parent_id,
        ];
    }
}
