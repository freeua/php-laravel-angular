<?php

namespace App\Portal\Http\Resources\V1\Employee;

use App\Http\Resources\BaseSearchResource;
use App\Portal\Http\Resources\V1\Company\ListCollections\ContractListCollection;
use App\Portal\Http\Resources\V1\Employee\ListCollections\OfferListCollection;
use App\Portal\Http\Resources\V1\Company\ListCollections\OrderListCollection;
use App\Portal\Services\Company\SearchService;

/**
 * Class SearchResource
 *
 * @package App\Portal\Http\Resources\V1\Employee
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
            'orders'    => $this->category == SearchService::CATEGORY_ORDERS ? new OrderListCollection($this->get('orders')) : $this->get('orders')
        ];
    }
}
