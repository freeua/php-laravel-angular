<?php

namespace App\Portal\Http\Resources\V1\ListCollections;

use App\Http\Resources\BaseListCollection;
use App\Models\Companies\Company;
use App\Portal\Models\Faq;
use Illuminate\Support\Collection;

/**
 * Class CompanyListCollection
 *
 * @package App\Portal\Http\Resources\V1\ListCollections
 */
class FAQListCollection extends BaseListCollection
{
    /**
     * Specifies data item in response
     *
     * @return Collection
     */
    protected function data(): Collection
    {
        return $this->collection->transform(function ($faq) {
            /** @var $faq FAQ */

            $company = Company::find($faq->company_id);

            return [
                'id'           => $faq->id,
                'question'     => $faq->question,
                'answer'       => $faq->answer,
                'author'       => $faq->author,
                'portal_id'    => $faq->portal_id,
                'category_id'  => $faq->category_id,
                'company_id'   => $faq->company_id,
                'company'      => $company ? $company->name : 'Alle',
            ];
        });
    }
}
