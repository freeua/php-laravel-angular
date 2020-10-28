<?php

namespace App\Portal\Http\Resources\V1;

use App\Portal\Models\ProductColor;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class AttributeResource
 *
 * @package App\Portal\Http\Resources\V1
 */
class ProductColorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    public function toArray($request)
    {
        /** @var $this ProductColor */
        return [
            'id' => $this->id,
            'name' => $this->name,
            'supplier' => $this->supplier,
        ];
    }
}
