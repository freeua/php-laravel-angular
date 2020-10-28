<?php


namespace App\Leasings\Services\ViewModels;

use App\Models\Identifier;
use App\Modules\TechnicalServices\Models\TechnicalService;
use App\Portal\Helpers\ContractPrices;
use Brick\Math\RoundingMode;
use Brick\Money\Money;

class TechnicalServiceNoteViewModel extends CreditNoteViewModel
{
    public $technicalServiceCreated = '';
    public $serviceCode = '';

    public function __construct(TechnicalService $technicalService)
    {
        $this->creditNoteNumber = Identifier::nextTechnicalServiceCreditNoteIdentifier();
        $this->fillSupplierData($technicalService->order);
        $this->fillTechnicalServiceData($technicalService);
        $this->fillProductData($technicalService->order);
        $this->fillPricesData($technicalService);
        $this->fillEmployeeData($technicalService->order);
        $this->fillLesseeData($technicalService->order);
    }

    private function fillTechnicalServiceData(TechnicalService $technicalService)
    {
        $this->technicalServiceCreated = $technicalService->createdAt->format('d.m.Y.');
        $this->serviceCode = $technicalService->inspectionCode;
    }

    private function fillPricesData(TechnicalService $technicalService)
    {
        $netPrice = Money::of($technicalService->grossAmount / ContractPrices::VAT_FACTOR, 'EUR', null, RoundingMode::HALF_UP);
        $grossPrice = Money::of($technicalService->grossAmount, 'EUR', null, RoundingMode::HALF_UP);
        $this->netTotal = $netPrice->formatTo('de');
        $this->vatTotal = $grossPrice->minus($netPrice, RoundingMode::HALF_UP)->formatTo('de');
        $this->grossTotal = $grossPrice->formatTo('de');
    }

    public function isLeasingCreditNote(): bool
    {
        return false;
    }

    public function isTechnicalServiceCreditNote(): bool
    {
        return true;
    }
}
