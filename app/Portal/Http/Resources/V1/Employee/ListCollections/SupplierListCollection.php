<?php

namespace App\Portal\Http\Resources\V1\Employee\ListCollections;

use App\Http\Resources\BaseListCollection;
use Illuminate\Support\Collection;

/**
 * Class SupplierListCollection
 *
 * @package App\Portal\Http\Resources\V1\ListCollections\Employee
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
            return [
                'id'            => $supplier->id,
                'name'          => $supplier->name,
                'city_name'     => $supplier->city_name,
                'zip'           => $supplier->zip,
                'address'       => $supplier->address,
                'logo'          => $supplier->logo,
                'admin_email'   => $supplier->admin_email,
                'phone'         => $supplier->phone
            ];
        });
    }
}
