<?php

namespace App\System\Http\Resources\Portal;

use App\Portal\Models\Product;
use App\System\Http\Resources\Collections\ProductImageCollection;
use App\System\Http\Resources\ProductCategoryResource;
use App\System\Http\Resources\SupplierSimpleResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

/**
 * Class ProductResource
 *
 * @package App\System\Http\Resources
 * @mixin Collection
 */
class ProductResource extends JsonResource
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
        /** @var $this Product */
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'color'        => $this->color,
            'size'        => $this->size,
            'brand_id'    => $this->brand_id,
            'image'       => $this->image,
            'images'      => new ProductImageCollection($this->whenLoaded('images')),
            'supplier'    => new SupplierSimpleResource($this->supplier),
            'category'    => new ProductCategoryResource($this->category),
            'brand'       => new ProductBrandResource($this->brand),
            'model'       => new ProductModelResource($this->model),
        ];
    }
}
