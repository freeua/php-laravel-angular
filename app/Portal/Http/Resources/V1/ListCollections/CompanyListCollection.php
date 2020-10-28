<?php

namespace App\Portal\Http\Resources\V1\ListCollections;

use App\Http\Resources\BaseListCollection;
use App\Models\Companies\Company;
use Illuminate\Support\Collection;

/**
 * Class CompanyListCollection
 *
 * @package App\Portal\Http\Resources\V1\ListCollections
 */
class CompanyListCollection extends BaseListCollection
{
    /**
     * Specifies data item in response
     *
     * @return Collection
     */
    protected function data(): Collection
    {
        return $this->collection->transform(function ($company) {
            /** @var $company Company */
            return [
                'id' => $company->id,
                'code' => $company->code,
                'name' => $company->name,
                'city_name' => $company->city->name,
                'employees_count' => $company->users->count(),
                'leasing_budget' => $company->leasing_budget,
                'spent_budget' => $company->spentLeasingBudget,
                'status' => $company->status,
                'end_contract' => $company->end_contract,
            ];
        });
    }
}
