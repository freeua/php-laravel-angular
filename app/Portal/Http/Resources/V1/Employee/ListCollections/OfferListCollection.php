<?php

namespace App\Portal\Http\Resources\V1\Employee\ListCollections;

use App\Http\Resources\BaseListCollection;
use App\Http\Resources\LeasingSettings\LeasingConditionResource;
use App\Http\Resources\LeasingSettings\RateResource;
use App\Portal\Http\Resources\V1\Employee\CompanyResource;
use App\Portal\Http\Resources\V1\ProductResource;
use App\Portal\Http\Resources\V1\SupplierResource;
use App\Portal\Models\Offer;
use Illuminate\Support\Collection;

/**
 * Class OfferListCollection
 *
 * @package App\Portal\Http\Resources\V1\Employee\ListCollections
 */
class OfferListCollection extends BaseListCollection
{
    /**
     * Specifies data item in response
     *
     * @return Collection
     */
    protected function data(): Collection
    {
        return $this->collection->transform(function (Offer $offer) {
            return [
                'id' => $offer->id,
                'number' => $offer->number,
                'username' => $offer->user->fullName,
                'product_brand'  => $offer->productBrand,
                'product_model'  => $offer->productModel,
                'product_size' => $offer->productSize,
                'product_color' => $offer->productColor,
                'supplier' => new SupplierResource($offer->supplier),
                'company' => new CompanyResource($offer->user->company),
                'discount_price' => $offer->productDiscountedPrice,
                'leasingCondition' => LeasingConditionResource::make($offer->user->company
                    ->activeLeasingConditionsByProductCategoryId($offer->productCategory->id)->first()),
                'insuranceRates' => RateResource::collection($offer->user->company->insuranceRates()
                    ->where('product_category_id', $offer->productCategory->id)->get()),
                'serviceRates' => RateResource::collection($offer->user->company->serviceRates()
                    ->where('product_category_id', $offer->productCategory->id)->get()),
                'normal_price' => $offer->productListPrice,
                'accessories_price' => $offer->accessoriesPrice,
                'contract_file' => $offer->contract_file,
                'status' => $offer->status,
                'expiry_date' => $offer->expiryDate,
            ];
        });
    }
}
