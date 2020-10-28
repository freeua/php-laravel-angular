<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class SearchResource
 *
 * @package App\Http\Resources
 */
abstract class BaseSearchResource extends JsonResource
{
    /** @var bool */
    protected $full;
    /** @var string */
    protected $category;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        $categories = $this->getCategoriesData();

        $result = [
            'categories' => $categories,
            'total'      => $this->get('total'),
        ];

        return $result;
    }

    /**
     * @return array
     */
    abstract protected function getCategoriesData(): array;

    /**
     * @param $value
     *
     * @return BaseSearchResource
     */
    public function category($value): BaseSearchResource
    {
        $this->category = $value;

        return $this;
    }

    /**
     * @param $value
     *
     * @return BaseSearchResource
     */
    public function full($value): BaseSearchResource
    {
        $this->full = $value;

        return $this;
    }
}
