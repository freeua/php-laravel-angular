<?php

namespace App\Portal\Services;

use App\Portal\Repositories\CompanyRepository;
use App\Portal\Repositories\SupplierRepository;
use App\Portal\Repositories\UserRepository;
use App\Services\BaseSearchService;

/**
 * Class SearchService
 *
 * @package App\Portal\Services
 */
class SearchService extends BaseSearchService
{
    const CATEGORY_COMPANIES = 'companies';

    const CATEGORY_SUPPLIERS = 'suppliers';

    const CATEGORY_USERS = 'users';

    /** @var CompanyRepository */
    private $companyRepository;
    /** @var SupplierRepository */
    private $supplierRepository;
    /** @var UserRepository */
    private $userRepository;

    /**
     * SearchService constructor.
     *
     * @param UserRepository     $userRepository
     * @param SupplierRepository $supplierRepository
     * @param CompanyRepository  $companyRepository
     */
    public function __construct(
        UserRepository $userRepository,
        SupplierRepository $supplierRepository,
        CompanyRepository $companyRepository
    ) {
        $this->userRepository = $userRepository;
        $this->supplierRepository = $supplierRepository;
        $this->companyRepository = $companyRepository;
    }

    /**
     * @return array
     */
    public static function getCategories(): array
    {
        return [
            self::CATEGORY_COMPANIES,
            self::CATEGORY_SUPPLIERS,
            self::CATEGORY_USERS
        ];
    }

    /**
     * @return array
     */
    public function getCategoryRepositoryMap(): array
    {
        return [
            self::CATEGORY_COMPANIES => $this->companyRepository,
            self::CATEGORY_SUPPLIERS => $this->supplierRepository,
            self::CATEGORY_USERS     => $this->userRepository
        ];
    }
}
