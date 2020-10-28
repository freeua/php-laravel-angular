<?php

namespace App\Leasings\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OfferListResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'number' => $this->number,
            'orderId' => isset($this->order) ? $this->order->id : null,
            'company' => CompanyResource::make($this->company),
            'product' => [
                'categoryId' => $this->productCategory->id,
                'brand' => $this->productBrand,
                'model' => $this->productModel,
                'color' => $this->productColor,
                'size' => $this->productSize,
            ],
            'supplier' => [
                'name' => $this->supplierName,
                'email' => $this->supplierEmail,
            ],
            'employee' => [
                'id' => $this->user->id,
                'name' => $this->employeeName,
                'email' => $this->employeeEmail,
            ],
            'pricing' => [
                'listPrice' => $this->productListPrice,
                'discountedPrice' => $this->productDiscountedPrice,
                'agreedPurchasePrice' => $this->agreedPurchasePrice,
                'accessoriesPrice' => $this->accessoriesPrice,
                'accessoriesDiscountedPrice' => $this->accessoriesDiscountedPrice,
            ],
            'status' => StatusResource::make($this->status),
            'expiryDate' => $this->expiryDate,
            'deliveryDate' => $this->deliveryDate,
            'createdAt' => $this->created_at,
        ];
    }
}
