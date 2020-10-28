<?php

namespace App\Portal\Http\Resources\V1;

use App\Models\LeasingCondition;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyLeasingSettingResource extends JsonResource
{
    public function toArray($request)
    {
        /** @var $this LeasingCondition */
        return [
            'id' => $this->id,
            'factor' => $this->factor,
            'period' => $this->period,
            'residualValue' => $this->residualValue,
            'activeAt' => $this->activeAt,
            'inactiveAt' => $this->inactiveAt,
            'productCategory' => new ProductCategoryResource($this->productCategory),
        ];
    }
}
