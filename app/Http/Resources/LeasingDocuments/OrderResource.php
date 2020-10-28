<?php

namespace App\Http\Resources\LeasingDocuments;

use App\Http\Resources\AuditResource;
use App\Portal\Http\Resources\V1\Employee\CompanyResource;
use App\Http\Resources\LeasingSettings\RateResource;
use App\Portal\Http\Resources\V1\OfferAccessoryResource;
use App\Portal\Models\Order;
use App\Portal\Models\User;
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
        $user = \Auth::user();
        $isEmployee = false;
        if ($user instanceof User) {
            $isEmployee = $user->isEmployee();
        }

        /** @var $this Order */
        return [
            'id' => $this->id,
            'number' => $this->number,
            'company' => CompanyResource::make($this->company),
            'pickup_code' => $this->when($isEmployee, $this->pickup_code),
            'product_id' => $this->product_id,
            'companyName' => $this->companyName,
            'accessories' => OfferAccessoryResource::collection($this->offer->accessories),
            'productCategory' => $this->productCategory,
            'productModel' => $this->productModel,
            'productBrand' => $this->productBrand,
            'productSize' => $this->productSize,
            'productColor' => $this->productColor,
            'supplierName' => $this->supplierName,
            'supplierCode' => $this->supplierCode,
            'supplierStreet' => $this->supplierStreet,
            'supplierCity' => $this->supplierCity,
            'supplierPostalCode' => $this->supplierPostalCode,
            'employeeName' => $this->employeeName,
            'employeeCode' => $this->employeeCode,
            'employeeSalutation' => $this->employeeSalutation,
            'employeeStreet' => $this->employeeStreet,
            'employeeCity' => $this->employeeCity,
            'employeePostalCode' => $this->employeePostalCode,
            'employeeEmail' => $this->employeeEmail,
            'employeePhone' => $this->employeePhone,
            'employeeNumber' => $this->employeeNumber,
            'sender' => $this->sender,
            'productListPrice' => $this->productListPrice,
            'productDiscountedPrice' => $this->productDiscountedPrice,
            'productDiscount' => $this->productDiscount,
            'agreedPurchasePrice' => $this->agreedPurchasePrice,
            'accessoriesPrice' => $this->accessoriesPrice,
            'accessoriesDiscountedPrice' => $this->accessoriesDiscountedPrice,
            'agreed_purchase_price' => $this->agreedPurchasePrice,
            'leasing_period' => $this->leasingPeriod,
            'leasing_rate' => $this->leasingRate,
            'insurance_rate_name' => $this->insuranceRateName,
            'service_rate_name' => $this->serviceRateName,
            'insurance_rate_amount' => $this->insuranceRateAmount,
            'service_rate_amount' => $this->serviceRateAmount,
            'leasing_rate_amount' => $this->leasingRateAmount,
            'insurance_rate' => $this->insuranceRate,
            'service_rate' => $this->serviceRate,
            'insuranceRate' => RateResource::make($this->offer->insuranceRate),
            'serviceRate' => RateResource::make($this->offer->serviceRate),
            'leasing_rate_subsidy' => $this->leasingRateSubsidy,
            'insurance_rate_subsidy' => $this->insuranceRateSubsidy,
            'service_rate_subsidy' => $this->serviceRateSubsidy,
            'calculated_residual_value' => $this->calculatedResidualValue,
            'taxRate' => $this->taxRate,
            'status' => $this->status,
            'notes' => $this->notes,
            'date' => $this->date,
            'offer' => OfferResource::make($this->whenLoaded('offer')),
            'accepted_at' => $this->acceptedAt ? $this->acceptedAt: null,
            'portalName' => $this->portal->name,
            'audits' => AuditResource::collection($this->when($this->relationLoaded('offer') && $this->offer->relationLoaded('audits'), $this->offer->audits)),
            'deliveryDate' => $this->offer->deliveryDate,
            'invoice_file' => $this->invoice_file,
        ];
    }
}
