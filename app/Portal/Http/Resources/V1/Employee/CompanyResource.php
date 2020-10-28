<?php

namespace App\Portal\Http\Resources\V1\Employee;

use App\Models\Companies\Company;
use App\Portal\Http\Resources\V1\CityResource;
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
            'zip' => $this->zip,
            'city_id' => $this->city_id,
            'city' => new CityResource($this->city),
            'address' => $this->address,
            'phone' => $this->phone,
            'insurance_covered' => $this->insurance_covered,
            'insurance_covered_type' => $this->insurance_covered_type,
            'insurance_covered_amount' => $this->insurance_covered_amount,
            'maintenance_covered' => $this->maintenance_covered,
            'maintenance_covered_type' => $this->maintenance_covered_type,
            'maintenance_covered_amount' => $this->maintenance_covered_amount,
            'leasing_rate' => $this->leasing_rate,
            'leasing_rate_type' => $this->leasing_rate_type,
            'leasing_rate_amount' => $this->leasing_rate_amount,
            'invoice_type' => $this->invoice_type,
            's_pedelec_disable' => $this->s_pedelec_disable,
        ];
    }
}
