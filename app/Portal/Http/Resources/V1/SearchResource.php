<?php

namespace App\Portal\Http\Resources\V1;

use App\Http\Resources\BaseSearchResource;
use App\Portal\Http\Resources\V1\ListCollections\CompanyListCollection;
use App\Portal\Http\Resources\V1\ListCollections\SupplierListCollection;
use App\Portal\Http\Resources\V1\ListCollections\UserListCollection;
use App\Portal\Services\SearchService;

/**
 * Class SearchResource
 *
 * @package App\Portal\Http\Resources\V1
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
            'companies' => $this->category == SearchService::CATEGORY_COMPANIES ? new CompanyListCollection($this->get('companies')) : $this->get('companies')
        ];
    }
}
