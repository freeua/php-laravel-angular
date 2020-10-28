<?php

namespace App\Portal\Helpers;

use App\Models\Companies\Company;
use App\Models\LeasingCondition;
use App\Models\Rates\InsuranceRate;
use App\Models\Rates\ServiceRate;
use App\Portal\Models\Offer;
use Brick\Math\RoundingMode;
use Brick\Money\Context\DefaultContext;
use Brick\Money\Money;

class ContractPrices
{
    const VAT_APPLIED = 19;
    const VAT_FACTOR = 1.19;
    /** @var Offer */
    private $offer;
    /** @var Company */
    private $company;
    /** @var LeasingCondition */
    private $leasingCondition;

    public function __construct(Offer $offer)
    {
        $this->offer = $offer;
        $this->company = $offer->company;
        $this->leasingCondition = $this->company->activeLeasingConditionsByProductCategory($offer->productCategory)
            ->first();
    }

    public function getGrossTotal(): Money
    {
        $total = Money::of($this->offer->productDiscountedPrice, 'EUR', null, RoundingMode::HALF_UP);
        if ($this->offer->accessoriesDiscountedPrice) {
            $total = $total->plus(Money::of($this->offer->accessoriesDiscountedPrice, 'EUR', null, RoundingMode::HALF_UP), RoundingMode::HALF_UP);
        }
        $blindDiscountAmount = $this->offer->blindDiscountAmount ? $this->offer->blindDiscountAmount : 0;
        return $total->minus($blindDiscountAmount, RoundingMode::HALF_UP);
    }

    public function getNetTotal(): Money
    {
        return $this->getGrossTotal()->dividedBy(self::VAT_FACTOR, RoundingMode::HALF_UP);
    }

    public function getVatApplied(): Money
    {
        return $this->getGrossTotal()->minus($this->getGrossTotal()->dividedBy(self::VAT_FACTOR, RoundingMode::HALF_UP), RoundingMode::HALF_UP);
    }

    public function getCalculationBasis(): Money
    {
        if ($this->company->invoice_type === Company::INVOICE_TYPE_NET) {
            return $this->getNetTotal();
        } else {
            return $this->getGrossTotal();
        }
    }

    public function getLeasingRate(): Money
    {
        $leasingFactorRatio = $this->leasingCondition->factor / 100;
        $amount = $this->getCalculationBasis()
            ->multipliedBy($leasingFactorRatio, RoundingMode::HALF_UP);
        return $amount;
    }

    public function getInsuranceRate(): Money
    {
        $amountType = $this->offer->insuranceRate->amountType;
        if ($this->company->include_insurance_rate == false) {
            return Money::zero('EUR');
        } elseif ($amountType == InsuranceRate::FIXED) {
            $amount = Money::of($this->offer->insuranceRate->amount, 'EUR', null, RoundingMode::HALF_UP);
            if ($this->company->invoice_type == Company::INVOICE_TYPE_GROSS) {
                $amount = $amount->multipliedBy(self::VAT_FACTOR, RoundingMode::HALF_UP);
            }
            return $amount;
        } else {
            $minimum = Money::of($this->offer->insuranceRate->minimum, 'EUR', null, RoundingMode::HALF_UP);
            $ratio = $this->offer->insuranceRate->amount / 100;
            $amount = $this->getCalculationBasis()->multipliedBy($ratio, RoundingMode::HALF_UP);
            if ($this->company->invoice_type == Company::INVOICE_TYPE_GROSS) {
                $minimum = $minimum->multipliedBy(self::VAT_FACTOR, RoundingMode::HALF_UP);
            }
            if ($amount->isLessThan($minimum)) {
                return $minimum;
            } else {
                return $amount;
            }
        }
    }

    public function getServiceRate(): Money
    {
        $amountType = $this->offer->serviceRate->amountType;
        if ($this->company->include_service_rate == false) {
            return Money::zero('EUR');
        } elseif ($amountType == ServiceRate::FIXED) {
            $amount = Money::of($this->offer->serviceRate->amount, 'EUR', null, RoundingMode::HALF_UP);
            if ($this->company->invoice_type == Company::INVOICE_TYPE_GROSS) {
                $amount = $amount->multipliedBy(self::VAT_FACTOR, RoundingMode::HALF_UP);
            }
            return $amount;
        } else {
            $minimum = Money::of($this->offer->serviceRate->minimum, 'EUR', null, RoundingMode::HALF_UP);
            $ratio = $this->offer->serviceRate->amount / 100;
            $amount = $this->getCalculationBasis()->multipliedBy($ratio, RoundingMode::HALF_UP);
            if ($this->company->invoice_type == Company::INVOICE_TYPE_GROSS) {
                $minimum = $minimum->multipliedBy(self::VAT_FACTOR, RoundingMode::HALF_UP);
            }
            if ($amount->isLessThan($minimum)) {
                return $minimum;
            } else {
                return $amount;
            }
        }
    }

    public function getLeasingRateCoverage(): Money
    {
        if ($this->offer->user->individual_settings && $this->offer->user->leasing_rate_subsidy) {
            if ($this->offer->user->leasing_rate_subsidy_type === Company::TYPE_FIXED) {
                $subsidyAmount = $this->offer->user->leasing_rate_subsidy_amount;
                if ($this->company->invoice_type == Company::INVOICE_TYPE_GROSS) {
                    $subsidyAmount = $subsidyAmount * self::VAT_FACTOR;
                }
                if ($this->getLeasingRate()->isLessThan($subsidyAmount)) {
                    return $this->getLeasingRate();
                }
                return Money::of($subsidyAmount, 'EUR', null, RoundingMode::HALF_UP);
            } else {
                $leasingRateCoverageRatio = $this->offer->user->leasing_rate_subsidy_amount / 100;
                return $this->getLeasingRate()
                    ->multipliedBy($leasingRateCoverageRatio, RoundingMode::HALF_UP);
            }
        } elseif ($this->offer->user->individual_settings && !$this->offer->user->leasing_rate_subsidy) {
            return Money::zero('EUR');
        } elseif ($this->company->leasing_rate) {
            if ($this->company->leasing_rate_type === Company::TYPE_FIXED) {
                $subsidyAmount = $this->company->leasing_rate_amount;
                if ($this->company->invoice_type == Company::INVOICE_TYPE_GROSS) {
                    $subsidyAmount = $subsidyAmount * self::VAT_FACTOR;
                }
                if ($this->getLeasingRate()->isLessThan($subsidyAmount)) {
                    return $this->getLeasingRate();
                }
                return Money::of($subsidyAmount, 'EUR', null, RoundingMode::HALF_UP);
            } else {
                $leasingRateCoverageRatio = $this->company->leasing_rate_amount / 100;
                return $this->getLeasingRate()
                    ->multipliedBy($leasingRateCoverageRatio, RoundingMode::HALF_UP);
            }
        } else {
            return Money::zero('EUR');
        }
    }

    public function getInsuranceCoverage(): Money
    {

        if ($this->offer->user->individual_settings && $this->offer->user->insurance_rate_subsidy) {
            if ($this->offer->user->insurance_rate_subsidy_type === Company::TYPE_FIXED) {
                $subsidyAmount = $this->offer->user->insurance_rate_subsidy_amount;
                if ($this->company->invoice_type == Company::INVOICE_TYPE_GROSS) {
                    $subsidyAmount = $subsidyAmount * self::VAT_FACTOR;
                }
                if ($this->getInsuranceRate()->isLessThan($subsidyAmount)) {
                    return $this->getInsuranceRate();
                }
                return Money::of($subsidyAmount, 'EUR', null, RoundingMode::HALF_UP);
            } else {
                $leasingRateCoverageRatio = $this->offer->user->insurance_rate_subsidy_amount / 100;
                return $this->getInsuranceRate()
                    ->multipliedBy($leasingRateCoverageRatio, RoundingMode::HALF_UP);
            }
        } elseif ($this->offer->user->individual_settings && !$this->offer->user->insurance_rate_subsidy) {
            return Money::zero('EUR');
        } elseif ($this->company->insurance_covered) {
            if ($this->company->include_insurance_rate == false) {
                return Money::zero('EUR');
            } elseif ($this->company->insurance_covered_type === Company::TYPE_FIXED) {
                $subsidyAmount = $this->company->insurance_covered_amount;
                if ($this->company->invoice_type == Company::INVOICE_TYPE_GROSS) {
                    $subsidyAmount = $subsidyAmount * self::VAT_FACTOR;
                }
                if ($this->getInsuranceRate()->isLessThan($subsidyAmount)) {
                    return $this->getInsuranceRate();
                }
                return Money::of($subsidyAmount, 'EUR', null, RoundingMode::HALF_UP);
            } else {
                $insuranceCoverageRatio = $this->company->insurance_covered_amount / 100;
                return $this->getInsuranceRate()
                    ->multipliedBy($insuranceCoverageRatio, RoundingMode::HALF_UP);
            }
        } else {
            return Money::zero('EUR');
        }
    }

    public function getServiceCoverage(): Money
    {
        if ($this->offer->user->individual_settings && $this->offer->user->service_rate_subsidy) {
            if ($this->offer->user->service_rate_subsidy_type === Company::TYPE_FIXED) {
                $subsidyAmount = $this->offer->user->service_rate_subsidy_amount;
                if ($this->company->invoice_type == Company::INVOICE_TYPE_GROSS) {
                    $subsidyAmount = $subsidyAmount * self::VAT_FACTOR;
                }
                if ($this->getServiceRate()->isLessThan($subsidyAmount)) {
                    return $this->getServiceRate();
                }
                return Money::of($subsidyAmount, 'EUR', null, RoundingMode::HALF_UP);
            } else {
                $leasingRateCoverageRatio = $this->offer->user->service_rate_subsidy_amount / 100;
                return $this->getServiceRate()
                    ->multipliedBy($leasingRateCoverageRatio, RoundingMode::HALF_UP);
            }
        } elseif ($this->offer->user->individual_settings && !$this->offer->user->service_rate_subsidy) {
            return Money::zero('EUR');
        } elseif ($this->company->maintenance_covered) {
            if ($this->company->include_service_rate == false) {
                return Money::zero('EUR');
            } elseif ($this->company->maintenance_covered_type === Company::TYPE_FIXED) {
                $subsidyAmount = $this->company->maintenance_covered_amount;
                if ($this->company->invoice_type == Company::INVOICE_TYPE_GROSS) {
                    $subsidyAmount = $subsidyAmount * self::VAT_FACTOR;
                }
                if ($this->getServiceRate()->isLessThan($subsidyAmount)) {
                    return $this->getServiceRate();
                }
                return Money::of($subsidyAmount, 'EUR', null, RoundingMode::HALF_UP);
            } else {
                $serviceCoverageRatio = $this->company->maintenance_covered_amount / 100;
                return $this->getServiceRate()
                    ->multipliedBy($serviceCoverageRatio, RoundingMode::HALF_UP);
            }
        } else {
            return Money::zero('EUR');
        }
    }

    public function getLeasingRateWithCoverage(): Money
    {
        return $this->getLeasingRate()->minus($this->getLeasingRateCoverage(), RoundingMode::HALF_UP);
    }

    public function getInsuranceWithCoverage(): Money
    {
        return $this->getInsuranceRate()->minus($this->getInsuranceCoverage(), RoundingMode::HALF_UP);
    }

    public function getServiceWithCoverage(): Money
    {
        return $this->getServiceRate()->minus($this->getServiceCoverage(), RoundingMode::HALF_UP);
    }

    public function getTotalRateWithoutCoverages(): Money
    {
        return $this->getLeasingRate()
            ->plus($this->getInsuranceRate(), RoundingMode::HALF_UP)
            ->plus($this->getServiceRate(), RoundingMode::HALF_UP)
            ->to(new DefaultContext(), RoundingMode::HALF_UP);
    }


    public function getTotalRateWithCoverages(): Money
    {
        return $this->getLeasingRateWithCoverage()
            ->plus($this->getInsuranceWithCoverage(), RoundingMode::HALF_UP)
            ->plus($this->getServiceWithCoverage(), RoundingMode::HALF_UP)
            ->to(new DefaultContext(), RoundingMode::HALF_UP);
    }

    public function getResidualValue(): Money
    {
        $residualRatio = $this->leasingCondition->residualValue / 100;
        return $this->getCalculationBasis()
            ->multipliedBy($residualRatio, RoundingMode::HALF_UP);
    }

    public function getLeasingConditionApplied(): LeasingCondition
    {
        return $this->leasingCondition;
    }
}
