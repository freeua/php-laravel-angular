<?php

namespace App\Portal\Http\Resources\V1\Supplier\ListCollections;

use App\Helpers\DateHelper;
use App\Http\Resources\BaseListCollection;
use Illuminate\Support\Collection;

/**
 * Class OfferListCollection
 *
 * @package App\Portal\Http\Resources\V1\Supplier\ListCollections
 */
class OfferListCollection extends BaseListCollection
{
    /**
     * Specifies data item in response
     *
     * @return Collection
     */
    protected function data(): Collection
    {
        return $this->collection->transform(function ($offer) {
            return [
                'id'             => $offer->id,
                'number'         => $offer->number,
                'username'       => $offer->username,
                'product_brand'  => $offer->product_brand,
                'product_name'   => $offer->product_name,
                'company_name'   => $offer->company_name,
                'discount_price' => $offer->bike_discounted_price,
                'date'           => $offer->date,
                'status'         => $offer->status
            ];
        });
    }
}
