<?php

namespace App\Portal\Services\Employee;

use App\Portal\Repositories\Employee\ContractRepository;
use App\Portal\Repositories\Employee\OfferRepository;
use App\Portal\Repositories\Employee\OrderRepository;
use App\Services\BaseSearchService;

/**
 * Class SearchService
 *
 * @package App\Portal\Services\Employee
 */
class SearchService extends BaseSearchService
{
    const CATEGORY_OFFERS = 'offers';

    const CATEGORY_ORDERS = 'orders';

    const CATEGORY_CONTRACTS = 'contracts';

    /** @var OrderRepository */
    private $orderRepository;
    /** @var OfferRepository */
    private $offerRepository;
    /** @var ContractRepository */
    private $contractRepository;
    /**
     * SearchService constructor.
     *
     * @param ContractRepository $contractRepository
     * @param OfferRepository    $offerRepository
     * @param OrderRepository    $orderRepository
     */
    public function __construct(
        ContractRepository $contractRepository,
        OfferRepository $offerRepository,
        OrderRepository $orderRepository
    ) {
        $this->contractRepository = $contractRepository;
        $this->offerRepository = $offerRepository;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @return array
     */
    public static function getCategories(): array
    {
        return [
            self::CATEGORY_OFFERS,
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
            self::CATEGORY_CONTRACTS => $this->contractRepository,
            self::CATEGORY_OFFERS    => $this->offerRepository,
            self::CATEGORY_ORDERS    => $this->orderRepository
        ];
    }
}
