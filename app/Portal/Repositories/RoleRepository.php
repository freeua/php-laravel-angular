<?php

namespace App\Portal\Repositories;

use App\Portal\Models\Role;
use App\Repositories\BaseRepository;

/**
 * Class RoleRepository
 *
 * @package App\Portal\Repositories
 *
 * @method Role find(int $id, array $relations = [])
 */
class RoleRepository extends BaseRepository
{
    /**
     * RoleRepository constructor.
     *
     * @param \App\Portal\Models\Role $role
     */
    public function __construct(Role $role)
    {
        $this->model = $role;
    }

    /**
     * @param string $role
     * @param bool   $login
     *
     * @return null|string
     */
    public function getRoute(string $role): ?string
    {
        return Role::getRoleRoute($role);
    }

    /**
     * @param string $role
     *
     * @return null|string
     */
    public function getModulePath(string $role): ?string
    {
        return Role::getRoleModulePath($role);
    }
}
