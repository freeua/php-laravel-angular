<?php

namespace App\Modules\TechnicalServices\Resources;

use App\Http\Resources\AuditResource;
use App\Http\Resources\LeasingDocuments\ContractResource;
use App\Models\Status;
use App\Modules\TechnicalServices\Models\TechnicalService;
use App\Portal\Helpers\AuthHelper;
use App\Portal\Http\Resources\V1\Employee\CompanyResource;
use Illuminate\Http\Resources\Json\JsonResource;

class TechnicalServiceResource extends JsonResource
{
    public function toArray($request)
    {
        $user = AuthHelper::user();
        $isSupplier = AuthHelper::isSupplier();

        /** @var $this TechnicalService */
        return [
            'id' => $this->id,
            'number' => $this->number,
            'company' => CompanyResource::make($this->whenLoaded('company')),
            'serviceModality' => $this->serviceModality,
            'contract'=> ContractResource::make($this->whenLoaded('contract')),
            'productSize' => $this->productSize,
            'productColor' => $this->productColor,
            'productModel' => $this->productModel,
            'productBrand' => $this->productBrand,
            'employeeCity' => $this->employeeCity,
            'employeePhone' => $this->employeePhone,
            'employeeEmail' => $this->employeeEmail,
            'employeeNumber' => $this->employeeNumber,
            'employeePostalCode' => $this->employeePostalCode,
            'employeeStreet' => $this->employeeStreet,
            'employeeName' => $this->employeeName,
            'employeeSalutation' => $this->employeeSalutation,
            'supplierCity' => $this->supplierCity,
            'supplierEmail' => $this->supplierEmail,
            'supplierPhone' => $this->supplierPhone,
            'supplierAdminName' => $this->supplierAdminName,
            'supplierName' => $this->supplierName,
            'supplierTaxId' => $this->supplierTaxId,
            'supplierBankName' => $this->supplierBankName,
            'supplierBankAccount' => $this->supplierBankAccount,
            'supplierCountry' => $this->supplierCountry,
            'supplierPostalCode' => $this->supplierPostalCode,
            'supplierStreet' => $this->supplierStreet,
            'senderName' => $this->senderName,
            'servicePdf' => $this->portal->servicePdf,
            'inspectionCode' => $user->id == $this->userId ? $this->inspectionCode : null,
            'pickupCode' => $user->id == $this->userId ? $this->pickupCode : null,
            'deliveryDate' => $this->deliveryDate,
            'employeeCode' => $this->employeeCode,
            'supplierCode' => $this->supplierCode,
            'frameNumber' => $this->frameNumber,
            'endDate' => $this->endDate,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'statusUpdatedAt' => $this->statusUpdatedAt,
            'sender' => $this->sender,
            'status' => $this->mapStatus($this),
            'audits' => AuditResource::collection($this->whenLoaded('audits')),
            'grossAmount' => $this->grossAmount,
        ];
    }

    private function mapStatus($technicalService)
    {
        if ($technicalService->status->id === TechnicalService::STATUS_OPEN && $technicalService->contract->isExpired()) {
            return Status::find(TechnicalService::STATUS_CANCELLED);
        }
        return $technicalService->status;
    }
}
