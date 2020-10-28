<?php

namespace App\Http\Resources\LeasingDocuments;

use App\Http\Resources\AuditResource;
use App\Modules\TechnicalServices\Resources\TechnicalServiceResource;
use App\Portal\Http\Resources\V1\Employee\CompanyResource;
use App\Portal\Models\Contract;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

/**
 * Class OrderResource
 *
 * @package App\Portal\Http\Resources\V1\Company
 * @mixin Collection
 */
class ContractResource extends JsonResource
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
        /** @var $this Contract */
        return [
            'id' => $this->id,
            'number' => $this->number,
            'company' => CompanyResource::make($this->company),
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
            'companyName' => $this->company->name,
            'sender' => $this->sender,
            'productListPrice' => $this->productListPrice,
            'productDiscountedPrice' => $this->productDiscountedPrice,
            'productDiscount' => $this->productDiscount,
            'agreedPurchasePrice' => $this->agreedPurchasePrice,
            'accessoriesPrice' => $this->accessoriesPrice,
            'accessoriesDiscountedPrice' => $this->accessoriesDiscountedPrice,
            'leasing_period' => $this->leasingPeriod,
            'leasing_rate' => $this->leasingRate,
            'insurance_rate_name' => $this->insuranceRateName,
            'service_rate_name' => $this->serviceRateName,
            'insurance_rate_amount' => $this->insuranceRateAmount,
            'service_rate_amount' => $this->serviceRateAmount,
            'leasing_rate_amount' => $this->leasingRateAmount,
            'insurance_rate' => $this->insuranceRate,
            'service_rate' => $this->serviceRate,
            'leasing_rate_subsidy' => $this->leasingRateSubsidy,
            'insurance_rate_subsidy' => $this->insuranceRateSubsidy,
            'service_rate_subsidy' => $this->serviceRateSubsidy,
            'calculated_residual_value' => $this->calculatedResidualValue,
            'taxRate' => $this->taxRate,
            'status' => $this->status,
            'notes' => $this->notes,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'accepted_at' => $this->accepted_at ? $this->accepted_at->format('d.m.Y') : null,
            'portalName' => $this->portal->name,
            'offer' => OfferResource::make($this->when(
                $this->relationLoaded('order') && $this->order->relationLoaded('offer'),
                $this->order->offer
            )),
            'audits' => AuditResource::collection($this->when(
                $this->relationLoaded('order') && $this->order->relationLoaded('offer')
                && $this->order->offer->relationLoaded('audits'),
                $this->order->offer->audits
            )),
            'cancellation_reason' => $this->cancellation_reason,
            'serialNumber' => $this->serialNumber,
            'lastTechnicalService' => TechnicalServiceResource::make($this->technicalServices()
                ->orderBy('created_at', 'DESC')->first()),
            'serviceRateModality' => $this->serviceRateModality,
            'remainingServiceBudget' => $this->remainingServiceBudget(),
            'serviceYearlyBudget' => $this->serviceBudget,
        ];
    }
}
