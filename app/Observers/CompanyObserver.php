<?php

namespace App\Observers;

use App\Models\Companies\Company;

class CompanyObserver
{
    public function created(Company $company)
    {
        $company->code = $company->generateCode($company->id, 6, '', 'FN-');
        $company->save();
    }
}
