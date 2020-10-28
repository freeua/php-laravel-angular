<?php

namespace App\Portal\Http\Resources\V1;

use App\Portal\Models\Offer;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

/**
 * Class OfferSimpleResource
 *
 * @package App\Portal\Http\Resources\V1
 * @mixin Collection
 */
class OfferSimpleResource extends JsonResource
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
        /** @var $this Offer */
        return [
            'id'     => $this->id,
            'number' => $this->number
        ];
    }
}
