<?php

namespace App\Portal\Http\Resources\V1\Supplier;

use App\Helpers\DateHelper;
use App\Http\Resources\LeasingSettings\LeasingConditionResource;
use App\Http\Resources\LeasingSettings\RateResource;
use App\Portal\Http\Resources\V1\CompanySimpleResource;
use App\Portal\Http\Resources\V1\OfferAccessoryResource;
use App\Portal\Http\Resources\V1\ProductResource;
use App\Portal\Http\Resources\V1\SupplierResource;
use App\Portal\Http\Resources\V1\UserResource;
use App\Portal\Models\Offer;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

/**
 * Class OfferResource
 *
 * @package App\Portal\Http\Resources\V1\Supplier
 * @mixin Collection
 */
class OfferResource extends JsonResource
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
        /** @var $this Offer */
        return [
            'id' => $this->id,
            'number' => $this->number,
            'product_id' => $this->product_id,
            'supplier' => SupplierResource::make($this->supplier),
            'employee' => new UserResource($this->user),
            'productModel' => $this->productModel,
            'productBrand' => $this->productBrand,
            'productSize' => $this->productSize,
            'productColor' => $this->productColor,
            'company' => new CompanySimpleResource($this->user->company),
            'leasingCondition' => LeasingConditionResource::make($this->user->company
                ->activeLeasingConditionsByProductCategoryId($this->productCategory->id)->first()),
            'insuranceRates' => RateResource::collection($this->user->company
                ->insuranceRatesByProductCategoryId($this->productCategory->id)->get()),
            'serviceRates' => RateResource::collection($this->user->company
                ->serviceRatesByProductCategoryId($this->productCategory->id)->get()),
            'insuranceRate' => RateResource::make($this->insuranceRate),
            'serviceRate' => RateResource::make($this->serviceRate),
            'bike_list_price' => $this->productListPrice,
            'bike_discounted_price' => $this->productDiscountedPrice,
            'bike_discount' => $this->productDiscount,
            'agreed_purchase_price' => $this->productDiscountedPrice + $this->accessoriesDiscountedPrice,
            'accessories_price' => $this->accessoriesPrice,
            'accessories_discounted_price' => $this->accessoriesDiscountedPrice,
            'accessories' => OfferAccessoryResource::collection($this->accessories),
            'product_notes' => $this->productNotes,
            'expiry_date' => $this->expiryDate,
            'notes' => $this->notes,
            'status' => $this->status
        ];
    }
}
