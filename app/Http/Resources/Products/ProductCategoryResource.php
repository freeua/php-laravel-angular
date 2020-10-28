<?php

namespace App\Http\Resources\Products;

use App\Http\Resources\LeasingSettings\LeasingConditionResource;
use App\Http\Resources\LeasingSettings\RateResource;
use App\Models\ProductCategory;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductCategoryResource extends JsonResource
{
    public function toArray($request)
    {
        /** @var $this ProductCategory */
        return [
            'id' => $this->id,
            'name' => $this->name,
            'leasingConditions' => LeasingConditionResource::collection($this->leasingConditions),
            'serviceRates' => RateResource::collection($this->serviceRates),
            'insuranceRates' => RateResource::collection($this->insuranceRates),
        ];
    }
}
