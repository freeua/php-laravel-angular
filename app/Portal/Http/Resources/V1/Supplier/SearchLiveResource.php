<?php

namespace App\Portal\Http\Resources\V1\Supplier;

use App\Http\Resources\BaseSearchLiveResource;
use App\Portal\Http\Resources\V1\Collections\UsersSimpleCollection;
use App\Portal\Http\Resources\V1\OfferSimpleResource;
use App\Portal\Http\Resources\V1\OrderSimpleResource;

/**
 * Class SearchLiveResource
 *
 * @package App\Portal\Http\Resources\V1\Supplier
 */
class SearchLiveResource extends BaseSearchLiveResource
{
    /**
     * @return array
     */
    public function getCategoriesData(): array
    {
        return [
            'offers' => OfferSimpleResource::collection($this->get('offers')),
            'orders' => OrderSimpleResource::collection($this->get('orders')),
            'users'  => new UsersSimpleCollection($this->get('users'))
        ];
    }
}
