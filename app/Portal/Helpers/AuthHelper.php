<?php

namespace App\Portal\Helpers;

use App\Models\Companies\Company;
use App\Portal\Models\Supplier;
use App\Portal\Models\User;
use App\System\Models\User as SystemUser;
use Illuminate\Support\Facades\Auth;

/**
 * Class AuthHelper
 *
 * @package App\Portal\Helpers
 */
abstract class AuthHelper
{
    /**
     * @return User|SystemUser|null
     */
    public static function user()
    {
        return Auth::user();
    }
    /**
     * @return int|null
     */
    public static function id(): ?int
    {
        if (!is_null(Auth::user())) {
            return Auth::user()->id;
        }
        return null;
    }

    /**
     * @return null|int
     */
    public static function companyId(): ?int
    {
        return Auth::user()->company_id;
    }

    /**
     * @return null|string
     */
    public static function companySlug(): ?string
    {
        return Auth::user()->company ? Auth::user()->company->slug : null;
    }

    /**
     * @return null|int
     */
    public static function supplierId(): ?int
    {
        return Auth::user()->supplier_id;
    }

    public static function supplier(): ?Supplier
    {
        return Auth::user()->supplier;
    }

    /**
     * @return string
     */
    public static function role(): string
    {
        return Auth::user()->getRoleNames()->first();
    }

    /**
     * @return string
     */
    public static function isAdmin(): string
    {
        return Auth::user()->isAdmin();
    }

    /**
     * @return string
     */
    public static function isCompanyAdmin(): string
    {
        return Auth::user()->isCompanyAdmin();
    }

    /**
     * @return string
     */
    public static function isEmployee(): string
    {
        if (Auth::user() instanceof User) {
            return Auth::user()->isEmployee();
        }
        return false;
    }

    public static function isSupplier(): string
    {
        if (Auth::user() instanceof User) {
            return Auth::user()->isSupplier();
        }
        return false;
    }

    /**
     * @return array
     */
    public static function relatedSupplierIds(): array
    {
        return self::company()->suppliers->pluck('id')->toArray();
    }

    /**
     * @return Company
     */
    public static function company(): Company
    {
        return Auth::user()->company;
    }
}
