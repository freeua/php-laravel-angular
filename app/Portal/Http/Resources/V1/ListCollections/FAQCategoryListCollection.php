<?php

namespace App\Portal\Http\Resources\V1\ListCollections;

use App\Http\Resources\BaseListCollection;
use App\Models\Companies\Company;
use App\Portal\Models\FaqCategory;
use Illuminate\Support\Collection;

/**
 * Class CompanyListCollection
 *
 * @package App\Portal\Http\Resources\V1\ListCollections
 */
class FaqCategoryListCollection extends BaseListCollection
{
    /**
     * Specifies data item in response
     *
     * @return Collection
     */
    protected function data(): Collection
    {
        return $this->collection->transform(function ($category) {
            /** @var $category FaqCategory */

            $company = Company::find($category->company_id);

            return [
                'id'                => $category->id,
                'name'              => $category->name,
                'description'       => $category->description,
                'portal_id'         => $category->portal_id,
                'company_id'        => $category->company_id,
                'company'           => $company ? $company->name : 'Alle',
            ];
        });
    }
}
