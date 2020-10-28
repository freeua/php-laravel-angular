<?php

namespace App\System\Http\Resources;

use App\Models\ProductCategory;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class ProductCategoryResource
 *
 * @package App\System\Http\Resources
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
        ];
    }
}
