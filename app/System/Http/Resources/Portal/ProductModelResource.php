<?php

namespace App\System\Http\Resources\Portal;

use App\Portal\Models\ProductModel;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class ProductModelResource
 *
 * @package App\System\Http\Resources\Portal
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
            'name' => $this->name
        ];
    }
}
