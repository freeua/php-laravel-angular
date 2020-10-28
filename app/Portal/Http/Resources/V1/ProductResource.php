<?php

namespace App\Portal\Http\Resources\V1;

use App\Portal\Models\Product;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

/**
 * Class ProductResource
 *
 * @package App\Portal\Http\Resources\V1
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
            'id' => $this->id,
            'brand_id' => $this->brand_id,
            'model_id' => $this->model_id,
            'image' => $this->image,
            'color' => $this->color,
            'size' => $this->size,
            'category' => new ProductCategoryResource($this->category()->withTrashed()->first()),
            'brand' => new ProductBrandResource($this->brand),
            'model' => new ProductModelResource($this->model),
            'supplier' => new SupplierSimpleResource($this->whenLoaded('supplier')),
        ];
    }
}
