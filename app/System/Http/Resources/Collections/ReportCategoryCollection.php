<?php

namespace App\System\Http\Resources\Collections;

use App\System\Models\ReportCategory;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Class ReportCategoryCollection
 *
 * @package App\System\Http\Resources\Collections
 */
class ReportCategoryCollection extends ResourceCollection
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return $this->collection->transform(function ($category) {
            /* @var $category ReportCategory */

            return [
                'id' => $category->id,
                'name' => $category->name,
            ];
        })->toArray();
    }
}
