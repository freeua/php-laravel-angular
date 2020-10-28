<?php

namespace App\System\Http\Resources;

use App\Portal\Models\Order;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

/**
 * Class OrderSimpleResource
 *
 * @package App\System\Http\Resources
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
