<?php

namespace App\Portal\Http\Resources\V1\Employee;

use App\Http\Resources\AuditResource;
use App\Portal\Http\Resources\V1\CityResource;
use App\Portal\Http\Resources\V1\ProductResource;
use App\Portal\Http\Resources\V1\SupplierResource;
use App\Portal\Models\Order;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

/**
 * Class OrderResource
 *
 * @package App\Portal\Http\Resources\V1\Company
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
            'id' => $this->id,
            'number' => $this->number,
            'pickup_code' => $this->pickup_code,
            'product_id' => $this->product_id,
            'product' => new ProductResource($this->product->load('supplier')),
            'supplier' => new SupplierResource($this->whenLoaded('supplier')),
            'zip' => $this->zip,
            'city_id' => $this->city_id,
            'city' => new CityResource($this->city),
            'address' => $this->address,
            'agreed_purchase_price' => $this->agreedPurchasePrice,
            'list_price' => $this->list_price,
            'leasing_period' => $this->leasingPeriod,
            'leasing_rate' => $this->leasingRate,
            'insurance_rate' => $this->insuranceRate,
            'service_rate' => $this->serviceRate,
            'leasing_rate_subsidy' => $this->leasingRateSubsidy,
            'insurance_rate_subsidy' => $this->insuranceRateSubsidy,
            'service_rate_subsidy' => $this->serviceRateSubsidy,
            'calculated_residual_value' => $this->calculatedResidualValue,
            'status' => $this->status,
            'notes' => $this->notes,
            'accepted_at' => $this->acceptedAt ? $this->acceptedAt->format('d.m.Y') : null,
        ];
    }
}
