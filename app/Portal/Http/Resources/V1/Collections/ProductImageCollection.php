<?php

namespace App\Portal\Http\Resources\V1\Collections;

use App\Portal\Models\ProductImage;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Class ProductImageCollection
 *
 * @package App\Portal\Http\Resources\V1\Collections
 */
class ProductImageCollection extends ResourceCollection
{
    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request): array
    {
        return $this->collection->transform(function ($category) {
            /* @var $category ProductImage */
            return [
                'id'   => $category->id,
                'name' => $category->name,
            ];
        })->toArray();
    }
}
