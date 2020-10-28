<?php

namespace App\System\Http\Resources\Collections;

use App\Portal\Models\ProductImage;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Class ProductImageCollection
 *
 * @package App\System\Http\Resources\Portal\Collections
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
