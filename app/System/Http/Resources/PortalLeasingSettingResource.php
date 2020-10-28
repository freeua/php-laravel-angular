<?php

namespace App\System\Http\Resources;

use App\Models\LeasingCondition;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class PortalLeasingSettingResource
 *
 * @package App\System\Http\Resources
 */
class PortalLeasingSettingResource extends JsonResource
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
            'id' => $this->id,
            'default' => $this->default,
            'factor' => $this->factor,
            'period' => $this->period,
            'residualValue' => $this->residual_value,
            'portalId' => $this->portal_id,
            'productCategory' => new ProductCategoryResource($this->productCategory),
        ];
    }
}
