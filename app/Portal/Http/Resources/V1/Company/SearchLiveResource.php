<?php

namespace App\Portal\Http\Resources\V1\Company;

use App\Http\Resources\BaseSearchLiveResource;
use App\Portal\Http\Resources\V1\Collections\UsersSimpleCollection;
use App\Portal\Http\Resources\V1\ContractSimpleResource;
use App\Portal\Http\Resources\V1\OfferSimpleResource;
use App\Portal\Http\Resources\V1\OrderSimpleResource;
use App\Portal\Http\Resources\V1\SupplierSimpleResource;

class SearchLiveResource extends BaseSearchLiveResource
{
    public function getCategoriesData(): array
    {
        return [
            'contracts' => ContractSimpleResource::collection($this->get('contracts')),
            'offers' => OfferSimpleResource::collection($this->get('offers')),
            'orders' => OrderSimpleResource::collection($this->get('orders')),
            'users' => new UsersSimpleCollection($this->get('users')),
            'suppliers' => SupplierSimpleResource::collection($this->get('suppliers')),
        ];
    }
}
