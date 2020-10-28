<?php

namespace App\System\Http\Resources;

use App\Http\Resources\BaseSearchLiveResource;
use App\System\Http\Resources\Collections\UsersSimpleCollection;

/**
 * Class SearchLiveResource
 *
 * @package App\System\Http\Resources
 */
class SearchLiveResource extends BaseSearchLiveResource
{
    /**
     * @return array
     */
    public function getCategoriesData(): array
    {
        return [
            'users'     => new UsersSimpleCollection($this->get('users')),
            'suppliers' => SupplierSimpleResource::collection($this->get('suppliers')),
            'portals'   => PortalSimpleResource::collection($this->get('portals')),
            'orders'    => OrderSimpleResource::collection($this->get('orders')),
            'contracts' => ContractSimpleResource::collection($this->get('contracts'))
        ];
    }
}
