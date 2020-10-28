<?php

namespace App\System\Http\Resources\ListCollections;

use App\Http\Resources\BaseListCollection;
use App\Portal\Models\Supplier;
use Illuminate\Support\Collection;

/**
 * Class SupplierListCollection
 *
 * @package App\System\Http\Resources\ListCollections
 */
class SupplierListCollection extends BaseListCollection
{
    /**
     * Specifies data item in response
     *
     * @return Collection
     */
    protected function data(): Collection
    {
        return $this->collection->transform(function ($supplier) {
            /** @var $supplier Supplier */
            return [
                'id'             => $supplier->id,
                'code'           => $supplier->code,
                'name'           => $supplier->name,
                'city_name'      => $supplier->city_name,
                'admin_email'    => $supplier->admin_email,
                'status'         => $supplier->status
            ];
        });
    }
}
