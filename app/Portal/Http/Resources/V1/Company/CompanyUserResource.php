<?php

namespace App\Portal\Http\Resources\V1\Company;

use App\Helpers\DateHelper;
use App\Portal\Http\Resources\V1\CityResource;
use App\Portal\Http\Resources\V1\CompanyResource;
use App\Portal\Http\Resources\V1\SupplierSimpleResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\AuditResource;

/**
 * Class CompanyUserResource
 * @package App\Portal\Http\Resources\V1\Company
 */
class CompanyUserResource extends JsonResource
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
        /** @var $this User */
        return [
            'id' => $this->id,
            'code' => $this->code,
            'company' => CompanyResource::make($this->company),
            'salutation' => $this->salutation,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'fullName' => $this->fullName,
            'email' => $this->email,
            'street' => $this->street,
            'city_id' => $this->city_id,
            'city' => new CityResource($this->city),
            'country' => $this->country,
            'employee_number' => $this->employee_number,
            'phone' => $this->phone,
            'postal_code' => $this->postal_code,
            'role' => $this->getRoleName(),
            'roles' => $this->roles->pluck('label'),
            'status' => $this->status,
            'created_at' => $this->created_at,
            'insurance_rate_subsidy' => $this->insurance_rate_subsidy,
            'insurance_rate_subsidy_type' => $this->insurance_rate_subsidy_type,
            'insurance_rate_subsidy_amount' => $this->insurance_rate_subsidy_amount,
            'service_rate_subsidy' => $this->service_rate_subsidy,
            'service_rate_subsidy_type' => $this->service_rate_subsidy_type,
            'service_rate_subsidy_amount' => $this->service_rate_subsidy_amount,
            'leasing_rate_subsidy' => $this->leasing_rate_subsidy,
            'leasing_rate_subsidy_type' => $this->leasing_rate_subsidy_type,
            'leasing_rate_subsidy_amount' => $this->leasing_rate_subsidy_amount,
            'max_user_contracts' => $this->max_user_contracts,
            'max_user_amount' => $this->max_user_amount,
            'min_user_amount' => $this->min_user_amount,
            'current_offers' => $this->offers()->count(),
            'current_orders' => $this->orders()->count(),
            'current_contracts' => $this->contracts()->count(),
            'individual_settings' => $this->individual_settings,
            'is_accept_offer' => $this->is_accept_offer,
            'audits' => AuditResource::collection($this->whenLoaded('audits'))
        ];
    }
}
