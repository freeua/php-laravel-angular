<?php

namespace App\System\Http\Resources\ListCollections;

use App\Helpers\DateHelper;
use App\Http\Resources\BaseListCollection;
use Illuminate\Support\Collection;

/**
 * Class OrderListCollection
 *
 * @package App\System\Http\Resources\ListCollections
 */
class OrderListCollection extends BaseListCollection
{
    /**
     * Specifies data item in response
     *
     * @return Collection
     */
    protected function data(): Collection
    {
        return $this->collection->transform(function ($order) {
            return [
                'id'            => $order->id,
                'number'        => $order->number,
                'date'          => $order->date,
                'username'      => $order->username,
                'supplier_name' => $order->supplier->name,
                'portal_name'   => $order->portal->name,
                'company_name'  => $order->company_name,
                'product_brand' => $order->product->brand->name,
                'product_name'  => $order->product_name,
                'status'        => $order->status
            ];
        });
    }
}
