<?php

namespace App\Portal\Http\Resources\V1\Supplier;

use App\Http\Resources\BaseSearchResource;
use App\Portal\Http\Resources\V1\Supplier\ListCollections\OfferListCollection;
use App\Portal\Http\Resources\V1\Supplier\ListCollections\OrderListCollection;
use App\Portal\Http\Resources\V1\Supplier\ListCollections\UserListCollection;
use App\Portal\Services\Supplier\SearchService;

/**
 * Class SearchResource
 *
 * @package App\Portal\Http\Resources\V1\Supplier
 */
class SearchResource extends BaseSearchResource
{
    /**
     * @return array
     */
    public function getCategoriesData(): array
    {
        return [
            'offers' => $this->category == SearchService::CATEGORY_OFFERS ? new OfferListCollection($this->get('offers')) : $this->get('offers'),
            'orders' => $this->category == SearchService::CATEGORY_ORDERS ? new OrderListCollection($this->get('orders')) : $this->get('orders'),
            'users'  => $this->category == SearchService::CATEGORY_USERS ? new UserListCollection($this->get('users')) : $this->get('users')
        ];
    }
}
