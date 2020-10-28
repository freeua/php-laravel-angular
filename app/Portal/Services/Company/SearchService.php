<?php

namespace App\Portal\Services\Company;

use App\Portal\Repositories\Company\SupplierRepository;
use App\Portal\Repositories\Company\UserRepository;
use App\Portal\Repositories\Company\ContractRepository;
use App\Portal\Repositories\Company\OfferRepository;
use App\Portal\Repositories\Company\OrderRepository;
use App\Services\BaseSearchService;

class SearchService extends BaseSearchService
{
    const CATEGORY_OFFERS = 'offers';

    const CATEGORY_ORDERS = 'orders';

    const CATEGORY_CONTRACTS = 'contracts';

    const CATEGORY_USERS = 'users';

    const CATEGORY_SUPPLIERS = 'suppliers';

    /** @var OrderRepository */
    private $orderRepository;
    /** @var OfferRepository */
    private $offerRepository;
    /** @var ContractRepository */
    private $contractRepository;
    /** @var UserRepository */
    private $userRepository;
    /**
     * @var SupplierRepository
     */
    private $supplierRepository;

    public function __construct(
        ContractRepository $contractRepository,
        OfferRepository $offerRepository,
        OrderRepository $orderRepository,
        UserRepository $userRepository,
        SupplierRepository $supplierRepository
    ) {
        $this->contractRepository = $contractRepository;
        $this->offerRepository = $offerRepository;
        $this->orderRepository = $orderRepository;
        $this->userRepository = $userRepository;
        $this->supplierRepository = $supplierRepository;
    }

    public static function getCategories(): array
    {
        return [
            self::CATEGORY_OFFERS,
            self::CATEGORY_ORDERS,
            self::CATEGORY_CONTRACTS,
            self::CATEGORY_USERS,
            self::CATEGORY_SUPPLIERS
        ];
    }

    public function getCategoryRepositoryMap(): array
    {
        return [
            self::CATEGORY_CONTRACTS => $this->contractRepository,
            self::CATEGORY_OFFERS => $this->offerRepository,
            self::CATEGORY_ORDERS => $this->orderRepository,
            self::CATEGORY_USERS => $this->userRepository,
            self::CATEGORY_SUPPLIERS => $this->supplierRepository,
        ];
    }
}
