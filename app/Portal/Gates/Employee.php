<?php

namespace App\Portal\Gates;

use App\Helpers\StorageHelper;
use App\Portal\Helpers\AuthHelper;
use App\Portal\Models\Offer;
use App\Portal\Models\User;
use App\Portal\Models\Contract;
use App\Portal\Models\Order;

/**
 * Class Employee
 *
 * @package App\Portal\Gates
 */
class Employee
{
    /**
     * @param User  $curUser
     * @param Offer $offer
     *
     * @return bool
     */
    public static function offer(User $curUser, Offer $offer): bool
    {
        return $curUser->id === $offer->userId;
    }

    /**
     * @param User  $curUser
     * @param Order $order
     *
     * @return bool
     */
    public static function order(User $curUser, Order $order): bool
    {
        return $curUser->id === $order->user_id;
    }

    /**
     * @param User     $curUser
     * @param Contract $contract
     *
     * @return bool
     */
    public static function contract(User $curUser, Contract $contract): bool
    {
        return $curUser->id === $contract->user_id;
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
}
