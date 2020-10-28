<?php

namespace App\Http\Resources\Orders;

use App\Models\Companies\Company;
use App\Models\Rates\ServiceRate;
use App\Portal\Helpers\ContractPrices;
use App\Portal\Models\Order;
use Brick\Math\RoundingMode;
use Brick\Money\Money;
use Illuminate\Http\Resources\Json\JsonResource;

class ExportedLeasingSettings extends JsonResource
{
    public $ratesAreGross;
    public function toArray($request)
    {
        $contractPrices = new ContractPrices($this->offer);

        $this->ratesAreGross = $this->offer->company->invoice_type == Company::INVOICE_TYPE_GROSS;

        return array_merge([
            "advantage" => 30,
            "advantage_gst" => 0,
            "contract_number_mlf" => false,
            "end_leasing" => $this->contract->endDate->format('Y-m-d H:i:s'),
            "end_mlf_tax_leasing" => $this->contract->endDate->format('Y-m-d H:i:s'),
            "capital_cost_reduction" => [],
            "grant" => 0,
            "id" => $contractPrices->getLeasingConditionApplied()->id,
            "insurance" => "always_leaserad",
            "insurance_rate" => round($this->getInsuranceRate($this) - $this->getInsuranceRateSubsidy($this), 2),
            "insurance_rate_mlf" => round($this->getInsuranceRateSubsidy($this), 2),// TODO
            "leasing_active_date" => $this->contract->start_date->format('Y-m-d H:i:s'),
            "leasing_rate" => round($this->getLeasingRate($this) - $this->getLeasingRateSubsidy($this), 2),
            "leasing_rate_mlf" => round($this->getLeasingRateSubsidy($this), 2),
            "residual" => $this->contract->calculatedResidualValue,
            "residual_mlf" => $this->contract->calculatedResidualValue,
            "start_leasing" => $this->contract->start_date->format('Y-m-d H:i:s'),
            "start_mlf_tax_leasing" => $this->contract->start_date->format('Y-m-d H:i:s'),
            "term" => $this->contract->leasingPeriod,
            "total_leasing_rate" => Money::of($this->getLeasingRate($this), 'EUR', null, RoundingMode::HALF_UP)
                ->plus($this->getInsuranceRate($this), RoundingMode::HALF_UP)
                ->plus($this->getServiceRate($this), RoundingMode::HALF_UP)
                ->getAmount()->toFloat(),
            "use_input_tax_deduction" => true,
            "work_way" => 0
        ], $this->getServiceOrInspection($this, $this->offer->serviceRate));
    }

    private function getServiceOrInspection($order, $serviceRate)
    {
        $isInspection = $serviceRate->type == ServiceRate::INSPECTION;
        $isFullService = $serviceRate->type == ServiceRate::FULL_SERVICE;
        $serviceRateValue = round($this->getServiceRate($order) - $this->getServiceRateSubsidy($order), 2);
        $serviceRateMlfValue = round($this->getServiceRateSubsidy($order), 2);
        return [
            "inspection_rate" => $isInspection ? $serviceRateValue : false,// TODO
            "inspection_rate_mlf" => $isInspection ? $serviceRateMlfValue : false,// TODO
            "inspection_voucher" => $isInspection ? "yes" : "no",
            "fullservice_rate" => $isFullService ? $serviceRateValue : false,
            "fullservice_rate_mlf" => $isFullService ? $serviceRateMlfValue : false,
            "fullservice_voucher" => $isFullService ? "yes" : "no",// TODO
        ];
    }

    private function getInsuranceRate($order)
    {
        return $this->ratesAreGross ?
            $order->insuranceRate / 1.19 :
            $order->insuranceRate;
    }

    private function getInsuranceRateSubsidy($order)
    {
        return $this->ratesAreGross ?
            $order->insuranceRateSubsidy / 1.19 :
            $order->insuranceRateSubsidy;
    }

    private function getServiceRate($order)
    {
        return $this->ratesAreGross ?
            $order->serviceRate / 1.19 :
            $order->serviceRate;
    }

    private function getServiceRateSubsidy($order)
    {
        return $this->ratesAreGross ?
            $order->serviceRateSubsidy / 1.19 :
            $order->serviceRateSubsidy;
    }

    private function getLeasingRate($order)
    {
        return $this->ratesAreGross ?
            $order->leasingRate / 1.19 :
            $order->leasingRate;
    }

    private function getLeasingRateSubsidy($order)
    {
        return $this->ratesAreGross ?
            $order->leasingRateSubsidy / 1.19 :
            $order->leasingRateSubsidy;
    }
}
