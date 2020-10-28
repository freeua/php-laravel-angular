<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class BaseSearchLiveResource
 *
 * @package App\Http\Resources
 */
abstract class BaseSearchLiveResource extends JsonResource
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
        return [
            'categories' => $this->getCategoriesData(),
            'total'      => $this->get('total'),
        ];
    }

    /**
     * @return array
     */
    abstract protected function getCategoriesData(): array;
}
