<?php

namespace App\Http\Resources\Users;

use App\Http\Resources\Companies\CompanyResource;
use App\Portal\Http\Resources\V1\SupplierSimpleResource;
use App\Portal\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        /** @var $this User */
        return [
            'id' => $this->id,
            'code' => $this->code,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'fullName' => $this->fullName,
            'email' => $this->email,
            'company_id' => $this->company_id,
            'portalId' => $this->portal_id,
            'company' => new CompanyResource($this->whenLoaded('company')),
            'supplier_id' => $this->supplier_id,
            'supplier' => new SupplierSimpleResource($this->whenLoaded('supplier')),
            'street' => $this->street,
            'city_id' => $this->city_id,
            'country' => $this->country,
            'employee_number' => $this->employee_number,
            'phone' => $this->phone,
            'postal_code' => $this->postal_code,
            'salutation' => $this->salutation,
            'insurance_rate_subsidy' => $this->insurance_rate_subsidy,
            'insurance_rate_subsidy_type' => $this->insurance_rate_subsidy_type,
            'insurance_rate_subsidy_amount' => $this->insurance_rate_subsidy_amount,
            'service_rate_subsidy' => $this->service_rate_subsidy,
            'service_rate_subsidy_type' => $this->service_rate_subsidy_type,
            'service_rate_subsidy_amount' => $this->service_rate_subsidy_amount,
            'leasing_rate_subsidy' => $this->leasing_rate_subsidy,
            'leasing_rate_subsidy_type' => $this->leasing_rate_subsidy_type,
            'leasing_rate_subsidy_amount' => $this->leasing_rate_subsidy_amount,
        ];
    }
}
