<?php

namespace App\Portal\Http\Resources\V1;

use App\Http\Resources\BaseSearchLiveResource;
use App\Portal\Http\Resources\V1\Collections\UsersSimpleCollection;

/**
 * Class SearchLiveResource
 *
 * @package App\Portal\Http\Resources\V1
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
            'companies' => CompanySimpleResource::collection($this->get('companies'))
        ];
    }
}
