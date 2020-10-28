<?php

namespace App\Portal\Http\Resources\V1;

use App\Models\LeasingCondition;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class LeasingSettingResource
 *
 * @package App\Portal\Http\Resources\V1
 */
class LeasingSettingResource extends JsonResource
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
        /** @var $this LeasingCondition */
        return [
            'id'                  => $this->id,
            'name'                => $this->name,
            'default'                => $this->default,
            'factor'              => $this->factor,
            'period'              => $this->period,
            'insurance'           => $this->insurance,
            'service_rate'        => $this->service_rate,
            'residual_value'      => $this->residual_value,
            'productCategoryId' => $this->product_category_id,
            'product_category'    => new ProductCategoryResource($this->whenLoaded('product_category')),
            'company'             => new CompanyResource($this->whenLoaded('company'))
        ];
    }
}
