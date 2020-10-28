<?php

namespace App\Portal\Gates;

use App\Helpers\StorageHelper;
use App\Modules\TechnicalServices\Models\TechnicalService;
use App\Portal\Models\Offer;
use App\Portal\Models\Order;
use App\Portal\Models\User;

/**
 * Class Supplier
 *
 * @package App\Portal\Gates
 */
class Supplier
{
    /**
     * @param User $curUser
     * @param User $user
     *
     * @return bool
     */
    public static function user(User $curUser, User $user): bool
    {
        return $curUser->supplier_id === $user->supplier_id;
    }

    /**
     * @param User  $curUser
     * @param Offer $offer
     *
     * @return bool
     */
    public static function offer(User $curUser, Offer $offer): bool
    {
        return $curUser->supplier_id === $offer->supplier->id;
    }

    /**
     * @param User  $curUser
     * @param Order $order
     *
     * @return bool
     */
    public static function order(User $curUser, Order $order): bool
    {
        return $curUser->supplier->id === $order->supplier_id;
    }

    /**
     * @param User        $curUser
     * @param null|string $path
     *
     * @return bool
     */
    public static function file(User $curUser, ?string $path = null): bool
    {
        return StorageHelper::userHasAccess($curUser, $path);
    }

    /**
     * @param User  $curUser
     * @param TechnicalService $technicalService
     *
     * @return bool
     */
    public static function technicalService(User $curUser, TechnicalService $technicalService): bool
    {
        return $curUser->supplier->id === $technicalService->supplierId;
    }
}
