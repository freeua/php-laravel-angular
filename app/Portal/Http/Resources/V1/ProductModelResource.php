<?php

namespace App\Portal\Http\Resources\V1;

use App\Portal\Models\ProductModel;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class ProductModelResource
 *
 * @package App\Portal\Http\Resources\V1
 */
class ProductModelResource extends JsonResource
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
        /** @var $this ProductModel */
        return [
            'id'   => $this->id,
            'name' => ltrim($this->name)
        ];
    }
}
