<?php


namespace App\Leasings\Services\ViewModels;

use App\Models\Identifier;
use App\Portal\Helpers\ContractPrices;
use App\Portal\Models\OfferAccessory;
use App\Portal\Models\Order;
use Brick\Math\RoundingMode;
use Brick\Money\Money;

class LeasingCreditNoteViewModel extends CreditNoteViewModel
{
    public $offerNumber = '';
    public $orderNumber = '';
    public $contractDate = '';
    public $pickupDate = '';
    public $grossTotal = '';


    public function __construct(Order $order)
    {
        $this->creditNoteNumber = Identifier::nextLeasingCreditNoteIdentifier();
        $this->fillSupplierData($order);
        $this->fillOrderData($order);
        $this->fillProductData($order);
        $this->fillPricesData($order);
        $this->fillEmployeeData($order);
        $this->fillLesseeData($order);
    }


    private function fillOrderData(Order $order)
    {
        $this->contractDate = $order->contract->startDate->format('d.m.Y');
        $this->orderNumber = $order->number;
        $this->offerNumber = $order->offer->number;
        $this->pickupDate = $order->pickedUpAt->format('d.m.Y');
    }


    private function fillPricesData(Order $order)
    {
        $netPrice = Money::of($order->agreedPurchasePrice / ContractPrices::VAT_FACTOR, 'EUR', null, RoundingMode::HALF_UP);
        $grossPirce = Money::of($order->agreedPurchasePrice, 'EUR', null, RoundingMode::HALF_UP);
        $this->netTotal = $netPrice->formatTo('de');
        $this->vatTotal = $grossPirce->minus($netPrice, RoundingMode::HALF_UP)->formatTo('de');
        $this->grossTotal = $grossPirce->formatTo('de');
    }

    public function isLeasingCreditNote(): bool
    {
        return true;
    }

    public function isTechnicalServiceCreditNote(): bool
    {
        return false;
    }
}
