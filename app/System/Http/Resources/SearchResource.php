<?php

namespace App\System\Http\Resources;

use App\Http\Resources\BaseSearchResource;
use App\System\Http\Resources\ListCollections\ContractListCollection;
use App\System\Http\Resources\ListCollections\OrderListCollection;
use App\System\Http\Resources\ListCollections\PortalListCollection;
use App\System\Http\Resources\ListCollections\SupplierListCollection;
use App\System\Http\Resources\ListCollections\UserListCollection;
use App\System\Services\SearchService;

/**
 * Class SearchResource
 *
 * @package App\System\Http\Resources
 */
class SearchResource extends BaseSearchResource
{
    /**
     * @return array
     */
    public function getCategoriesData(): array
    {
        return [
            'users'     => $this->category == SearchService::CATEGORY_USERS ? new UserListCollection($this->get('users')) : $this->get('users'),
            'suppliers' => $this->category == SearchService::CATEGORY_SUPPLIERS ? new SupplierListCollection($this->get('suppliers')) : $this->get('suppliers'),
            'portals'   => $this->category == SearchService::CATEGORY_PORTALS ? new PortalListCollection($this->get('portals')) : $this->get('portals'),
            'orders'    => $this->category == SearchService::CATEGORY_ORDERS ? new OrderListCollection($this->get('orders')) : $this->get('orders'),
            'contracts' => $this->category == SearchService::CATEGORY_CONTRACTS ? new ContractListCollection($this->get('contracts')) : $this->get('contracts')
        ];
    }
}
