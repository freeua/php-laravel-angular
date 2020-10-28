<?php

namespace App\Portal\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class OfferAccessoryResource extends JsonResource
{
    public function toArray($request)
    {
        /* @var $this \App\Portal\Models\OfferAccessory */
        return [
            'name' => $this->name,
            'amount' => $this->amount,
            'price' => $this->price,
            'discount' => $this->discount,
            'discounted_price' => $this->discounted_price,
        ];
    }
}
