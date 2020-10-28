<?php

namespace App\Portal\Http\Resources\V1\Company\ListCollections;

use App\Helpers\DateHelper;
use App\Http\Resources\BaseListCollection;
use Illuminate\Support\Collection;

/**
 * Class ContractListCollection
 *
 * @package App\Portal\Http\Resources\V1\Company\ListCollections
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
                'id' => $contract->id,
                'number' => $contract->number,
                'username' => $contract->username,
                'product_brand' => $contract->product_brand,
                'product_name' => $contract->product_name,
                'leasing_rate' => $contract->leasing_rate,
                'start_date' => $contract->start_date,
                'end_date' => $contract->end_date,
                'status' => $contract->status,
                'insurance_rate' => $contract->insurance_rate,
                'service_rate' => $contract->service_rate,
                'leasing_rate_subsidy' => $contract->leasing_rate_subsidy,
                'insurance_rate_subsidy' => $contract->insurance_rate_subsidy,
                'service_rate_subsidy' => $contract->service_rate_subsidy,
                'cancellation_reason' => $contract->cancellation_reason,
            ];
        });
    }
}
