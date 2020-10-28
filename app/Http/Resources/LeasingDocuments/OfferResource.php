<?php

namespace App\Http\Resources\LeasingDocuments;

use App\Http\Resources\AuditResource;
use App\Http\Resources\LeasingSettings\LeasingConditionResource;
use App\Http\Resources\LeasingSettings\RateResource;
use App\Http\Resources\Users\UserResource;
use App\Models\Status;
use App\Portal\Helpers\ContractPrices;
use App\Portal\Http\Resources\V1\Employee\CompanyResource;
use App\Portal\Http\Resources\V1\OfferAccessoryResource;
use App\Portal\Models\Offer;
use App\Portal\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class OfferResource extends JsonResource
{
    public function toArray($request)
    {
        if ($this->status_id == Offer::STATUS_DRAFT || $this->status_id == Offer::STATUS_PENDING) {
            return $this->openOffer();
        } else {
            return $this->closedOffer();
        }
    }

    private function baseOffer(): array
    {
        /** @var $this Offer */
        $serviceRates = $this->user->company
            ->serviceRatesByProductCategoryId($this->productCategory->id)->get();
        /** @var $this Offer */
        $insuranceRates = $this->user->company
            ->insuranceRatesByProductCategoryId($this->productCategory->id)->get();
        /** @var $this Offer */
        return [
            'id' => $this->id,
            'number' => $this->number,
            'company' => CompanyResource::make($this->company),
            'employee' => UserResource::make($this->user),
            'productCategory' => $this->productCategory,
            'productModel' => $this->productModel,
            'productBrand' => $this->productBrand,
            'productSize' => $this->productSize,
            'productColor' => $this->productColor,
            'supplierId' => $this->supplier_id,
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
            'senderName' => $this->senderName,
            'senderId' => $this->senderId,
            'productListPrice' => $this->productListPrice,
            'productDiscountedPrice' => $this->productDiscountedPrice,
            'productDiscount' => $this->productDiscount,
            'agreedPurchasePrice' => $this->agreedPurchasePrice,
            'accessoriesPrice' => $this->accessoriesPrice,
            'accessoriesDiscountedPrice' => $this->accessoriesDiscountedPrice,
            'accessories' => OfferAccessoryResource::collection($this->accessories),
            'notes' => $this->notes,
            'contract_file' => $this->contract_file,
            'offerPdf' => $this->offerPdf,
            'contract_generated' => $this->user->hasAllContractFields(),
            'status' => $this->getStatus(),
            'blindDiscountAmount' => $this->blindDiscountAmount,
            'expiryDate' => $this->expiryDate,
            'deliveryDate' => $this->deliveryDate,
            'createdAt' => $this->created_at,
            'audits' => AuditResource::collection($this->whenLoaded('audits')),
            'insuranceRates' => RateResource::collection($insuranceRates),
            'serviceRates' => RateResource::collection($serviceRates),
        ];
    }

    private function openOffer()
    {
        /** @var $this Offer */
        $serviceRates = $this->user->company
            ->serviceRatesByProductCategoryId($this->productCategory->id)->get();
        /** @var $this Offer */
        $insuranceRates = $this->user->company
            ->insuranceRatesByProductCategoryId($this->productCategory->id)->get();
        $pricesHelper = new ContractPrices($this->resource);
        return array_merge(
            $this->baseOffer(),
            [
                'leasingCondition' => LeasingConditionResource::make($this->user->company
                    ->activeLeasingConditionsByProductCategoryId($this->productCategory->id)->first()),
                'insuranceRate' => $this->insuranceRate ? RateResource::make($this->insuranceRate) : $insuranceRates[0],
                'serviceRate' => $this->serviceRate ? RateResource::make($this->serviceRate) : $serviceRates[0],
                'leasingRateAmount' => $pricesHelper->getLeasingRate()->getAmount()->toFloat(),
                'insuranceRateAmount' => $pricesHelper->getInsuranceRate()->getAmount()->toFloat(),
                'serviceRateAmount' => $pricesHelper->getServiceRate()->getAmount()->toFloat(),
                'leasingRateSubsidy' => $pricesHelper->getLeasingRateCoverage()->getAmount()->toFloat(),
                'insuranceRateSubsidy' => $pricesHelper->getInsuranceCoverage()->getAmount()->toFloat(),
                'serviceRateSubsidy' => $pricesHelper->getServiceCoverage()->getAmount()->toFloat(),
                'taxRate' => $pricesHelper->getTotalRateWithCoverages()->isZero() ? 0
                : floor(($this->productListPrice + $this->accessoriesPrice) * 0.005),
            ]
        );
    }

    private function closedOffer()
    {
        return array_merge(
            $this->baseOffer(),
            [
                'leasingCondition' => LeasingConditionResource::make($this->user->company
                    ->activeLeasingConditionsByProductCategoryId($this->productCategory->id)->first()),
                'insuranceRate' => RateResource::make($this->insuranceRate),
                'serviceRate' => RateResource::make($this->serviceRate),
                'insuranceRateAmount' => $this->insuranceRateAmount,
                'serviceRateAmount' => $this->serviceRateAmount,
                'leasingRateAmount' => $this->leasingRateAmount,
                'insuranceRateSubsidy' => $this->insuranceRateSubsidy,
                'serviceRateSubsidy' => $this->serviceRateSubsidy,
                'leasingRateSubsidy' => $this->leasingRateSubsidy,
                'taxRate' => $this->taxRate,
            ]
        );
    }

    private function getStatus()
    {
        $user = request()->user();
        if ($user instanceof User && $user->isSupplier()) {
            return $this->mapSupplierStatus($this->status);
        } else {
            return $this->status;
        }
    }

    private function mapSupplierStatus($status)
    {
        switch ($status->id) {
            case Offer::STATUS_ACCEPTED:
            case Offer::STATUS_PENDING_APPROVAL:
                return Status::query()->find(Offer::STATUS_PENDING);
            default:
                return $status;
        }
    }
}
