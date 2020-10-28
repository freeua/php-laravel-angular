<?php

namespace App\System\Http\Resources\Portal;

use App\Portal\Models\Supplier;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class SupplierResource
 *
 * @package App\System\Http\Resources\Portal
 */
class SupplierResource extends JsonResource
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
        /** @var $this Supplier */
        return [
            'id'                 => $this->id,
            'name'               => $this->name,
            'vat'                => $this->vat,
            'city_id'            => $this->city_id,
            'address'            => $this->address,
            'phone'              => $this->phone,
            'status'             => $this->status,
            'products_count'     => $this->products_count
        ];
    }
}
