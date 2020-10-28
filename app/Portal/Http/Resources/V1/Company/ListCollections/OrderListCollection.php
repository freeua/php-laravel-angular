<?php

namespace App\Portal\Http\Resources\V1\Company\ListCollections;

use App\Http\Resources\BaseListCollection;
use Illuminate\Support\Collection;

/**
 * Class OrderListCollection
 *
 * @package App\Portal\Http\Resources\V1\Company\ListCollections
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
                'id' => $order->id,
                'number' => $order->number,
                'username' => $order->username,
                'product_brand' => $order->product_brand,
                'product_name' => $order->product_name,
                'supplier_name' => $order->supplier_name,
                'agreed_purchase_price' => $order->agreed_purchase_price,
                'leasing_rate' => $order->leasing_rate,
                'status' => $order->status,
                'insurance_rate' => $order->insurance_rate,
                'service_rate' => $order->service_rate,
                'leasing_rate_subsidy' => $order->leasing_rate_subsidy,
                'insurance_rate_subsidy' => $order->insurance_rate_subsidy,
                'service_rate_subsidy' => $order->service_rate_subsidy,
            ];
        });
    }
}
