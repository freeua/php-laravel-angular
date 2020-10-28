<?php

namespace App\Portal\Services\Company;

use App\Portal\Helpers\AuthHelper;
use App\Portal\Repositories\CompanyRepository;
use Illuminate\Support\Collection;

/**
 * Class CompanyService
 *
 * @package App\Portal\Services\Company
 */
class CompanyService
{
    /** @var CompanyRepository */
    protected $companyRepository;

    /**
     * CompanyService constructor.
     *
     * @param CompanyRepository $companyRepository
     */
    public function __construct(
        CompanyRepository $companyRepository
    ) {
        $this->companyRepository = $companyRepository;
    }

    /**
     * @param array $ids
     *
     * @return Collection|false
     */
    public function storeSuppliers(array $ids)
    {
        $company = AuthHelper::user()->company;

        /** @var Collection $exitsLeasingSettingsIds */
        $exitsSuppliersIds = $company->suppliers->pluck('id', 'id');
        $currentSuppliersIds = collect($ids)->filter();

        $this->companyRepository->detachSuppliers($company, $exitsSuppliersIds->diff($currentSuppliersIds)->toArray());
        $company->suppliers()->attach($currentSuppliersIds->diff($exitsSuppliersIds));

        return $company->fresh()->suppliers;
    }
}
