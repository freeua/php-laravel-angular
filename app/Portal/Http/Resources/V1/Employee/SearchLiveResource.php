<?php

namespace App\Portal\Http\Resources\V1\Employee;

use App\Http\Resources\BaseSearchLiveResource;
use App\Portal\Http\Resources\V1\ContractSimpleResource;
use App\Portal\Http\Resources\V1\OfferSimpleResource;
use App\Portal\Http\Resources\V1\OrderSimpleResource;

/**
 * Class SearchLiveResource
 *
 * @package App\Portal\Http\Resources\V1\Employee
 */
class SearchLiveResource extends BaseSearchLiveResource
{
    /**
     * @return array
     */
    public function getCategoriesData(): array
    {
        return [
            'contracts' => ContractSimpleResource::collection($this->get('contracts')),
            'offers'    => OfferSimpleResource::collection($this->get('offers')),
            'orders'    => OrderSimpleResource::collection($this->get('orders'))
        ];
    }
}
