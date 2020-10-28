<?php

namespace App\Http\Resources\LeasingSettings;

use App\Http\Resources\Products\ProductCategoryResource;
use App\Models\LeasingCondition;
use Illuminate\Http\Resources\Json\JsonResource;

class LeasingConditionResource extends JsonResource
{
    public function toArray($request)
    {
        /** @var $this LeasingCondition */
        return [
            'id'                    => $this->id,
            'factor'                => $this->factor,
            'period'                => $this->period,
            'residualValue'        => $this->residualValue,
            'productCategoryId'   => $this->productCategoryId,
            'activeAt'             => $this->activeAt,
            'inactiveAt'           => $this->inactiveAt,
            'productCategory'      => new ProductCategoryResource($this->whenLoaded('productCategory')),
        ];
    }
}
