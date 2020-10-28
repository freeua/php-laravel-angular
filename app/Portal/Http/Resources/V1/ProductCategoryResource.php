<?php

namespace App\Portal\Http\Resources\V1;

use App\Models\ProductCategory;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class ProductCategoryResource
 *
 * @package App\Portal\Http\Resources\V1
 */
class ProductCategoryResource extends JsonResource
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
        /** @var $this ProductCategory */
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'service_rate' => $this->service_rate
        ];
    }
}
