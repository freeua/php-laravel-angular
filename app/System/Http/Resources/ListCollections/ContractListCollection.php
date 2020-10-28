<?php

namespace App\System\Http\Resources\ListCollections;

use App\Helpers\DateHelper;
use App\Http\Resources\BaseListCollection;
use Illuminate\Support\Collection;

/**
 * Class ContractListCollection
 *
 * @package App\System\Http\Resources\ListCollections
 */
class ContractListCollection extends BaseListCollection
{
    /**
     * Specifies data item in response
     *
     * @return Collection
     */
    protected function data(): Collection
    {
        return $this->collection->transform(function ($contract) {
            return [
                'id'           => $contract->id,
                'number'       => $contract->number,
                'start_date'   => $contract->start_date,
                'end_date'     => $contract->end_date,
                'username'     => $contract->username,
                'portal_name'  => $contract->portal->name,
                'product_brand'=> $contract->product->brand->name,
                'product_name' => $contract->product_name,
                'status'       => $contract->status,
                'cancellation_reason' => $contract->cancellation_reason
            ];
        });
    }
}
