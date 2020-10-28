<?php

namespace App\Portal\Http\Resources\V1\Company\ListCollections;

use App\Http\Resources\BaseListCollection;
use Illuminate\Support\Collection;

/**
 * Class OfferListCollection
 *
 * @package App\Portal\Http\Resources\V1\Company\ListCollections
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
                'id'            => $offer->id,
                'number'        => $offer->number,
                'username'      => $offer->username,
                'product_brand' => $offer->product_brand,
                'product_name'  => $offer->product_name,
                'supplier_name' => $offer->supplier_name,
                'normal_price'  => $offer->normal_price,
                'contract_file' => $offer->contract_file,
                'status'        => $offer->status
            ];
        });
    }
}
