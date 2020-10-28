<?php

namespace App\Portal\Http\Resources\V1;

use App\Portal\Models\Order;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

/**
 * Class OrderSimpleResource
 *
 * @package App\Portal\Http\Resources\V1
 * @mixin Collection
 */
class OrderSimpleResource extends JsonResource
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
        /** @var $this Order */
        return [
            'id'     => $this->id,
            'number' => $this->number
        ];
    }
}
