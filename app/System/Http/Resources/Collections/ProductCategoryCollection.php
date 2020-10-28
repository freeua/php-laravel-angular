<?php

namespace App\System\Http\Resources\Collections;

use App\Models\ProductCategory;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Class ProductCategoryCollection
 *
 * @package App\System\Http\Resources\Portal\Collections
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
                'id'          => $category->id,
                'name'        => $category->name,
                'is_active'   => $category->is_active,
            ];
        })->toArray();
    }
}
