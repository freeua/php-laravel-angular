<?php

namespace App\Portal\Services\Supplier;

use App\Portal\Repositories\Supplier\UserRepository;
use App\Portal\Repositories\Supplier\OfferRepository;
use App\Portal\Repositories\Supplier\OrderRepository;
use App\Services\BaseSearchService;

/**
 * Class SearchService
 *
 * @package App\Portal\Services\Supplier
 */
class SearchService extends BaseSearchService
{
    const CATEGORY_OFFERS = 'offers';

    const CATEGORY_ORDERS = 'orders';

    const CATEGORY_USERS = 'users';

    /** @var OrderRepository */
    private $orderRepository;
    /** @var OfferRepository */
    private $offerRepository;
    /** @var UserRepository */
    private $userRepository;

    /**
     * SearchService constructor.
     *
     * @param OfferRepository    $offerRepository
     * @param OrderRepository    $orderRepository
     * @param UserRepository     $userRepository
     */
    public function __construct(
        OfferRepository $offerRepository,
        OrderRepository $orderRepository,
        UserRepository $userRepository
    ) {
        $this->offerRepository = $offerRepository;
        $this->orderRepository = $orderRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @return array
     */
    public static function getCategories(): array
    {
        return [
            self::CATEGORY_OFFERS,
            self::CATEGORY_ORDERS,
            self::CATEGORY_USERS
        ];
    }

    /**
     * @return array
     */
    public function getCategoryRepositoryMap(): array
    {
        return [
            self::CATEGORY_OFFERS    => $this->offerRepository,
            self::CATEGORY_ORDERS    => $this->orderRepository,
            self::CATEGORY_USERS     => $this->userRepository
        ];
    }
}
