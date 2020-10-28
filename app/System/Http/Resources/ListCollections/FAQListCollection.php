<?php

namespace App\System\Http\Resources\ListCollections;

use App\Http\Resources\BaseListCollection;
use App\Models\Portal;
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

            $portal = Portal::find($faq->portal_id);

            return [
                'id'           => $faq->id,
                'question'     => $faq->question,
                'answer'       => $faq->answer,
                'author'       => $faq->author,
                'visible'      => $faq->visible,
                'portal'       => $portal ? $portal->name : 'Alle',
                'category_id'  => $faq->category_id,
            ];
        });
    }
}
