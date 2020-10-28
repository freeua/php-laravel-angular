<?php

namespace App\Portal\Http\Resources\V1\Collections;

use App\Models\ProductCategory;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Class ProductCategoryCollection
 *
 * @package App\Portal\Http\Resources\V1\Collections
 */
class ProductCategoryCollection extends ResourceCollection
{
    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request): array
    {
        return $this->collection->transform(function ($category) {
            /* @var $category ProductCategory */
            return [
                'id'   => $category->id,
                'name' => $category->name,
            ];
        })->toArray();
    }
}
