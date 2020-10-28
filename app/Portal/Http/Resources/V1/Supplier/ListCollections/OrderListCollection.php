<?php

namespace App\Portal\Http\Resources\V1\Supplier\ListCollections;

use App\Helpers\DateHelper;
use App\Http\Resources\BaseListCollection;
use Illuminate\Support\Collection;

/**
 * Class OrderListCollection
 *
 * @package App\Portal\Http\Resources\V1\Supplier\ListCollections
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
                'id'           => $order->id,
                'number'       => $order->number,
                'username'     => $order->username,
                'price'        => $order->agreed_purchase_price,
                'date'         => $order->date,
                'product_brand'=> $order->product->brand->name,
                'product_name' => $order->product_name,
                'company_name' => $order->company_name,
                'status'       => $order->status,
                'status_id'    => $order->status_id
            ];
        });
    }
}
