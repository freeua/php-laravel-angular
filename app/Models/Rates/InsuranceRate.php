<?php

namespace App\Models\Rates;

class InsuranceRate extends Rate
{
    protected $table = 'insurance_rates';

    public function makeDefault()
    {
        $this->portal->defaultInsuranceRateByProduct($this->productCategory)->update(['default' => 0]);
        $this->update(['default' => true]);
    }
}
