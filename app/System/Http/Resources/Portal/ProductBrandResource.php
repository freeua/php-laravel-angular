<?php

namespace App\System\Http\Resources\Portal;

use App\Portal\Models\ProductBrand;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class ProductBrandResource
 *
 * @package App\System\Http\Resources\Portal
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
