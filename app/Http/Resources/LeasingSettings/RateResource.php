<?php

namespace App\Http\Resources\LeasingSettings;

use App\Http\Resources\Products\ProductCategoryResource;
use App\Models\Rates\Rate;
use Illuminate\Http\Resources\Json\JsonResource;

class RateResource extends JsonResource
{
    public function authorize()
    {
        return true;
    }

    public function toArray($request)
    {
        /** @var $this Rate */
        return [
            'id' => $this->id,
            'name' => $this->name,
            'amount' => $this->amount,
            'amountType' => $this->amountType,
            'minimum' => $this->minimum,
            'productCategory' => new ProductCategoryResource($this->whenLoaded('productCategory')),
            'default' => $this->default,
            'type' => $this->when(isset($this->type), $this->type),
            'budget' => $this->budget,
        ];
    }
}
