<?php

namespace App\Http\Resources\Companies;

use App\Models\Companies\Company;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        /** @var $this Company */
        return [
            'id' => $this->id,
            'name'=> $this->name,
            'insurance_covered' => $this->insurance_covered,
            'insurance_covered_type' => $this->insurance_covered_type,
            'insurance_covered_amount' => $this->insurance_covered_amount,
            'maintenance_covered' => $this->maintenance_covered,
            'maintenance_covered_type' => $this->maintenance_covered_type,
            'maintenance_covered_amount' => $this->maintenance_covered_amount,
            'leasing_rate' => $this->leasing_rate,
            'leasing_rate_type' => $this->leasing_rate_type,
            'leasing_rate_amount' => $this->leasing_rate_amount,
        ];
    }
}
