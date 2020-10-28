<?php


namespace App\Leasings\Services\ViewModels;

use App\Models\Identifier;
use App\Portal\Helpers\ContractPrices;
use App\Portal\Models\OfferAccessory;
use App\Portal\Models\Order;
use Brick\Math\RoundingMode;
use Brick\Money\Money;

abstract class CreditNoteViewModel
{
    public $creditNoteNumber = '';
    public $gpNumber = '';
    public $supplierName = '';
    public $supplierStreet = '';
    public $supplierPostalCode = '';
    public $supplierCity = '';
    public $supplierBankName = '';
    public $supplierBankAccount = '';
    public $supplierTaxId = '';
    public $productAmount = '';
    public $productCategory = '';
    public $productBrand = '';
    public $productModel = '';
    public $productColor = '';
    public $productSize = '';
    public $accessories = '';
    public $productSerialNumber = '';
    public $netTotal = '';
    public $vatTotal = '';
    public $grossTotal = '';
    public $employeeId = '';
    public $employeeName = '';
    public $employeeStreet = '';
    public $employeePostalCode = '';
    public $employeeCity = '';
    public $lesseeGpNumber = '';
    public $lesseeBoniNumber = '';
    public $lesseeName = '';
    public $lesseeCompany = '';
    public $lesseeStreet = '';
    public $lesseePostalCode = '';
    public $lesseeCity = '';

    abstract public function isLeasingCreditNote() : bool;

    abstract public function isTechnicalServiceCreditNote(): bool;

    protected function fillSupplierData(Order $order)
    {
        $this->supplierPostalCode = $order->supplierPostalCode;
        $this->supplierStreet = $order->supplierStreet;
        $this->supplierName = $order->supplierName;
        $this->supplierBankName = $order->supplierBankName;
        $this->supplierBankAccount = $order->supplierBankAccount;
        $this->supplierTaxId = $order->supplierTaxId;
        $this->supplierPostalCode = $order->supplierPostalCode;
        $this->supplierCity = $order->supplierCity;
        $this->gpNumber = $order->supplierGpNumber;
    }

    protected function fillProductData(Order $order)
    {
        $this->productAmount = 1;
        $this->productBrand = $order->productBrand;
        $this->productCategory = $order->productCategory->name;
        $this->productModel = $order->productModel;
        $this->productSize = $order->productSize;
        $this->productColor = $order->productColor;
        $this->productSerialNumber = $order->frameNumber;
        $this->accessories = $order->offer->accessories->map(function (OfferAccessory $accessory) {
            return "$accessory->amount x $accessory->name";
        })->join('<br>');
    }

    protected function fillEmployeeData(Order $order)
    {
        $this->employeeCity = $order->employeeCity;
        $this->employeeName = $order->employeeName;
        $this->employeeId = $order->employeeCode;
        $this->employeePostalCode = $order->employeePostalCode;
        $this->employeeStreet = $order->employeeStreet;
    }

    protected function fillLesseeData(Order $order)
    {
        $company = $order->company;
        $this->lesseeBoniNumber = $company->boni_number;
        $this->lesseeGpNumber = $company->gp_number;
        $this->lesseeId = $company->code;
        $this->lesseeCity = $company->city->name;
        $this->lesseeCompany = $company->name;
        $this->lesseePostalCode = $company->zip;
        $this->lesseeStreet = $company->address;
    }
}
