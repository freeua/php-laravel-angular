<?php

namespace App\Portal\Gates;

use App\Helpers\StorageHelper;
use App\Models\Permission;
use App\Portal\Models\Offer;
use App\Portal\Models\Role;
use App\Portal\Models\User;
use App\Portal\Models\Widget;
use App\Portal\Models\Contract;
use App\Portal\Models\Order;

/**
 * Class Company
 *
 * @package App\Portal\Gates
 */
class Company
{
    /**
     * @param User $curUser
     * @param User $user
     *
     * @return bool
     */
    public static function user(User $curUser, User $user): bool
    {
        return $curUser->company_id === $user->company_id;
    }

    /**
     * @param User $curUser
     * @param User $user
     *
     * @return bool
     */
    public static function manageUsers(User $curUser, User $user): bool
    {
        $subcompanies = $curUser->company->subcompanies()->get(['id']);
        return ($curUser->company_id === $user->company_id || $subcompanies->contains('id', $user->company_id)) &&
            $curUser->hasPermissionTo(Permission::MANAGE_COMPANY_EMPLOYEES, Role::COMPANY_GUARD);
    }

    public static function editCompanyData(User $user)
    {
        return $user->hasPermissionTo(Permission::EDIT_COMPANY_DATA, Role::COMPANY_GUARD);
    }

    public static function readCompanyData(User $user)
    {
        return $user->hasPermissionTo(Permission::READ_COMPANY_DATA, Role::COMPANY_GUARD);
    }

    /**
     * @param User  $curUser
     * @param Offer $offer
     *
     * @return bool
     */
    public static function offer(User $curUser, Offer $offer): bool
    {
        return self::checkCompanyId($curUser->company_id, $offer->user->company_id);
    }

    /**
     * @param User  $curUser
     * @param Order $order
     *
     * @return bool
     */
    public static function order(User $curUser, Order $order): bool
    {
        return self::checkCompanyId($curUser->company_id, $order->company_id);
    }

    /**
     * @param int  $currentCompanyId
     * @param int $companyId
     *
     * @return bool
     */
    private static function checkCompanyId(int $currentCompanyId, int $companyId): bool
    {
        if ($currentCompanyId === $companyId) {
            return true;
        } else {
            $company = \App\Models\Companies\Company::where('parent_id', '=', $currentCompanyId)->first();
            if (!$company) {
                return false;
            }
            return $company->id === $companyId;
        }

        return false;
    }

    /**
     * @param User     $curUser
     * @param Contract $contract
     *
     * @return bool
     */
    public static function contract(User $curUser, Contract $contract): bool
    {
        return self::checkCompanyId($curUser->company_id, $contract->company_id);
    }

    /**
     * @param User   $curUser
     * @param Widget $widget
     *
     * @return bool
     */
    public static function widget(User $curUser, Widget $widget): bool
    {
        return $curUser->id === $widget->user_id;
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
