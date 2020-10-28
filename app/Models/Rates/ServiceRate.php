<?php

namespace App\Models\Rates;

/**
 * @property string type
 * @property int $budget
 */
class ServiceRate extends Rate
{
    const FULL_SERVICE = 'fullservice';
    const INSPECTION = 'inspection';
    protected $table = 'service_rates';

    public function makeDefault()
    {
        $this->portal->defaultServiceRateByProduct($this->productCategory)->update(['default' => false]);
        $this->update(['default' => true]);
    }
}
