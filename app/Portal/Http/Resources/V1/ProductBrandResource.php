<?php

namespace App\Portal\Http\Resources\V1;

use App\Portal\Models\ProductBrand;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class ProductBrandResource
 *
 * @package App\Portal\Http\Resources\V1
 */
class ProductBrandResource extends JsonResource
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
        /** @var $this ProductBrand */
        return [
            'id'   => $this->id,
            'name' => $this->name
        ];
    }
}
