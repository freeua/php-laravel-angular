<?php

namespace App\Portal\Models;

use Spatie\Permission\Models\Role as BaseRole;

/**
 * Class Role
 *
 * @package App\Portal
 */
class Role extends BaseRole
{
    const GUARD_API = 'api';
    const COMPANY_GUARD = 'company';

    const ROLE_PORTAL_ADMIN = 'Portal Admin';

    const ROLE_COMPANY_ADMIN = 'Company Admin';

    const ROLE_SUPPLIER_ADMIN = 'Supplier Admin';

    const ROLE_EMPLOYEE = 'Employee';

    /**
     * @param bool $login
     *
     * @return array
     */
    public static function getRoleRouteMap(bool $login = false): array
    {
        return [
            self::ROLE_PORTAL_ADMIN   => 'portal-api',
            self::ROLE_SUPPLIER_ADMIN => 'supplier-api',
            self::ROLE_COMPANY_ADMIN  => 'company-api',
            self::ROLE_EMPLOYEE       => 'company-api',
        ];
    }

    /**
     * @param bool $login
     *
     * @return array
     */
    public static function getRouteRoleMap(bool $login = false): array
    {
        return [
            'portal-api' => self::ROLE_PORTAL_ADMIN,
            'supplier-api' => self::ROLE_SUPPLIER_ADMIN,
            'company-api' => [self::ROLE_COMPANY_ADMIN, self::ROLE_EMPLOYEE],
            'employee-api' => self::ROLE_EMPLOYEE,
        ];
    }

    /**
     * @param string $role
     * @param bool   $login
     *
     * @return null|string
     */
    public static function getRoleRoute(string $role): ?string
    {
        $routes = Role::getRoleRouteMap();

        return $routes[$role] ?? null;
    }

    /**
     * @return array
     */
    public static function getRoleModulePathMap(): array
    {
        return [
            self::ROLE_PORTAL_ADMIN   => '/portal',
            self::ROLE_SUPPLIER_ADMIN => '/lieferanten',
            self::ROLE_COMPANY_ADMIN  => '/firma/{companySlug}',
            self::ROLE_EMPLOYEE       => '/firma/{companySlug}',
        ];
    }

    /**
     * @param string $role
     *
     * @return null|string
     */
    public static function getRoleModulePath(string $role): ?string
    {
        $paths = Role::getRoleModulePathMap();

        return $paths[$role] ?? null;
    }
}
