<?php

namespace App\Modules\TechnicalServices\Resources;

use App\Models\Status;
use App\Modules\TechnicalServices\Models\TechnicalService;
use App\Portal\Helpers\AuthHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class TechnicalServiceListCollection extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        $isEmployee = AuthHelper::isEmployee();
        /** @var $this TechnicalService */
        return [
            'id' => $this->id,
            'number' => $this->number,//id
            'frameNumber' => $this->frameNumber,//rehmenum
            'productBrand' => $this->productBrand,//marke
            'productModel' => $this->productModel,//model
            'company' => $this->company,//Firma
            'employeeName' => $this->employeeName,//Mitarbeiter
            'employeeEmail' => $this->employeeEmail,//Mitarbeiter
            'employeeRole' => $this->user->getRoleName(), //role
            'employeeStatus' => $this->user->status, //role
            'status' => $this->mapStatus($this), //Status
            'statusId' => $this->statusId, //Servicemodalität
            'inspectionCode' => $isEmployee ? $this->inspectionCode : null,
            'pickupCode' => $isEmployee ? $this->pickupCode : null,
            'contractNumber' => $this->contract->number, //vertage ID
            'serviceModality' => $this->serviceModality, //service modality
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
