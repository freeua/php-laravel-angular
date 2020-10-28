<?php

namespace App\Portal\Http\Resources\V1\Supplier;

use App\Portal\Http\Resources\V1\ProductResource;
use App\Portal\Http\Resources\V1\SupplierSimpleResource;
use App\Portal\Http\Resources\V1\UserResource;
use App\Portal\Models\Order;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

/**
 * Class OrderResource
 *
 * @package App\Portal\Http\Resources\V1\Supplier
 * @mixin Collection
 */
class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        /** @var $this Order */
        return [
            'id'              => $this->id,
            'code'            => $this->number,
            'user_id'         => $this->user_id,
            'contract_number' => $this->contract->number,
            'user'            => new UserResource($this->user->load('company')),
            'supplier_id'     => $this->supplier_id,
            'supplier'        => new SupplierSimpleResource($this->supplier),
            'product_id'      => $this->product_id,
            'product'         => new ProductResource($this->product->load('supplier')),
            'status'          => $this->status,
            'status_id'       => $this->status_id,
            'notes'           => $this->notes,
        ];
    }
}
