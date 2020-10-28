<?php

namespace App\Portal\Http\Resources\V1\Company;

use App\Http\Resources\BaseSearchResource;
use App\Portal\Http\Resources\V1\Company\ListCollections\ContractListCollection;
use App\Portal\Http\Resources\V1\Company\ListCollections\OfferListCollection;
use App\Portal\Http\Resources\V1\Company\ListCollections\OrderListCollection;
use App\Portal\Http\Resources\V1\Company\ListCollections\UserListCollection;
use App\Portal\Services\Company\SearchService;

/**
 * Class SearchResource
 *
 * @package App\Portal\Http\Resources\V1\Company
 */
class SearchResource extends BaseSearchResource
{
    /**
     * @return array
     */
    public function getCategoriesData(): array
    {
        return [
            'contracts' => $this->category == SearchService::CATEGORY_CONTRACTS ? new ContractListCollection($this->get('contracts')) : $this->get('contracts'),
            'offers'    => $this->category == SearchService::CATEGORY_OFFERS ? new OfferListCollection($this->get('offers')) : $this->get('offers'),
            'orders'    => $this->category == SearchService::CATEGORY_ORDERS ? new OrderListCollection($this->get('orders')) : $this->get('orders'),
            'users'     => $this->category == SearchService::CATEGORY_USERS ? new UserListCollection($this->get('users')) : $this->get('users')
        ];
    }
}
