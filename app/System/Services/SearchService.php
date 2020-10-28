<?php

namespace App\System\Services;

use App\Services\BaseSearchService;
use App\System\Repositories\ContractRepository;
use App\Repositories\OrderRepository;
use App\System\Repositories\PortalRepository;
use App\System\Repositories\SupplierRepository;
use App\System\Repositories\UserRepository;

/**
 * Class SearchService
 *
 * @package App\System\Services
 */
class SearchService extends BaseSearchService
{
    const CATEGORY_ORDERS = 'orders';

    const CATEGORY_CONTRACTS = 'contracts';

    const CATEGORY_USERS = 'users';

    const CATEGORY_SUPPLIERS = 'suppliers';

    const CATEGORY_PORTALS = 'portals';

    /** @var PortalRepository */
    private $portalRepository;
    /** @var SupplierRepository */
    private $supplierRepository;
    /** @var ContractRepository */
    private $contractRepository;
    /** @var OrderRepository */
    private $orderRepository;
    /** @var UserRepository */
    private $userRepository;

    /**
     * SearchService constructor.
     *
     * @param OrderRepository    $orderRepository
     * @param ContractRepository $contractRepository
     * @param UserRepository     $userRepository
     * @param SupplierRepository $supplierRepository
     * @param PortalRepository   $portalRepository
     */
    public function __construct(
        OrderRepository $orderRepository,
        ContractRepository $contractRepository,
        UserRepository $userRepository,
        SupplierRepository $supplierRepository,
        PortalRepository $portalRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->contractRepository = $contractRepository;
        $this->userRepository = $userRepository;
        $this->supplierRepository = $supplierRepository;
        $this->portalRepository = $portalRepository;
    }

    /**
     * @return array
     */
    public static function getCategories(): array
    {
        return [
            self::CATEGORY_USERS,
            self::CATEGORY_SUPPLIERS,
            self::CATEGORY_PORTALS,
            self::CATEGORY_ORDERS,
            self::CATEGORY_CONTRACTS
        ];
    }

    /**
     * @return array
     */
    public function getCategoryRepositoryMap(): array
    {
        return [
            self::CATEGORY_SUPPLIERS => $this->supplierRepository,
            self::CATEGORY_USERS     => $this->userRepository,
            self::CATEGORY_PORTALS   => $this->portalRepository,
            self::CATEGORY_ORDERS    => $this->orderRepository,
            self::CATEGORY_CONTRACTS => $this->contractRepository
        ];
    }
}
