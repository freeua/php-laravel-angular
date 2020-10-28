<?php

namespace App\Portal\Repositories;

use App\Helpers\PortalHelper;
use App\Portal\Models\User;
use App\Portal\Models\Role;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Class UserRepository
 *
 * @package App\Portal\Repositories
 *
 * @method User find(int $id, array $relations = [])
 */
class UserRepository extends Base\UserRepository
{
    protected $filterWhereColumns = [
        'company' => 'company',
        'role'    => 'role',
    ];
    protected $searchHavingColumns = [
        'code',
        'email',
        'company',
        'role',
        'name',
    ];

    /**
     * @inheritdoc
     */
    public function list(array $params, array $relationships = ['status']): LengthAwarePaginator
    {
        $query = $this->newQuery();

        $query->select([
            'portal_users.id',
            'portal_users.code',
            'portal_users.email',
            'portal_users.status_id',
            'r.name as role',
            \DB::raw('CONCAT(portal_users.first_name, " ", portal_users.last_name) as name'),
            \DB::raw('IF(r.name = "' . Role::ROLE_PORTAL_ADMIN . '", "' . PortalHelper::name() . '", c.name) as company')
        ])
            ->join('model_has_roles as mhr', function (JoinClause $join) {
                $join->on('mhr.model_id', '=', 'portal_users.id')
                    ->where('mhr.model_type', '=', User::class);
            })
            ->join('roles as r', 'r.id', '=', 'mhr.role_id')
            ->leftJoin('companies as c', 'portal_users.company_id', '=', 'c.id')
            ->whereIn('r.name', User::getManageableRoles());


        $query->where('portal_users.portal_id', '=', PortalHelper::id());

        if (!empty($params['status_id'])) {
            $query->where(['portal_users.status_id' => $params['status_id']]);
        }

        if ($relationships) {
            $query->with($relationships);
        }

        $query = $this->applyFilters($query, $params);

        if (!empty($params['search'])) {
            $query = $this->applySearch($query, $params['search']);
        }

        $orderBy = $params['order_by'] ?? 'id';
        $order = $params['order'] ?? 'desc';
        $perPage = $params['per_page'] ?? $this->perPage;
        $page = $params['page'] ?? 1;
        $items = $query
            ->orderBy($orderBy, $order)
            ->get();
        $ids = [];
        $newItems = new Collection();
        foreach ($items as $item) {
            if (!in_array($item->id, $ids)) {
                $ids[] = $item->id;
                $newItems->add($item);
            }
        }
        $result = $newItems
            ->slice(($page - 1) * $perPage, $perPage)
            ->all();

        return new LengthAwarePaginator(array_values($result), count($newItems), $perPage);
    }

    public function findAllEmployees(array $params, array $relationships = ['status']): LengthAwarePaginator
    {
        $query = $this->newQuery();
        $role = Role::ROLE_EMPLOYEE;

        $query->select([
            'portal_users.id',
            'portal_users.code',
            'portal_users.email',
            'portal_users.status_id',
            'r.name as role',
            \DB::raw('CONCAT(portal_users.first_name, " ", portal_users.last_name) as name'),
            \DB::raw('IF(r.name = "' . Role::ROLE_PORTAL_ADMIN . '", "' . PortalHelper::name() . '", c.name) as company')
        ])
            ->join('model_has_roles as mhr', function (JoinClause $join) {
                $join->on('mhr.model_id', '=', 'portal_users.id')
                    ->where('mhr.model_type', '=', User::class);
            })
            ->join('roles as r', 'r.id', '=', 'mhr.role_id')
            ->leftJoin('companies as c', 'portal_users.company_id', '=', 'c.id')
            ->where('r.name', '=', $role);


        $query->where('portal_users.portal_id', '=', PortalHelper::id());

        if (!empty($params['status_id'])) {
            $query->where(['portal_users.status_id' => $params['status_id']]);
        }

        if ($relationships) {
            $query->with($relationships);
        }

        $query = $this->applyFilters($query, $params);

        if (!empty($params['search'])) {
            $query = $this->applySearch($query, $params['search']);
        }

        $orderBy = $params['order_by'] ?? 'id';
        $order = $params['order'] ?? 'desc';
        $perPage = $params['per_page'] ?? $this->perPage;
        $page = $params['page'] ?? 1;
        $items = $query
            ->orderBy($orderBy, $order)
            ->get();
        $ids = [];
        $newItems = new Collection();
        foreach ($items as $item) {
            if (!in_array($item->id, $ids)) {
                $ids[] = $item->id;
                $newItems->add($item);
            }
        }
        $result = $newItems
            ->slice(($page - 1) * $perPage, $perPage)
            ->all();

        return new LengthAwarePaginator(array_values($result), count($newItems), $perPage);
    }

    /**
     * @inheritdoc
     */
    public function searchTotal(array $params): int
    {
        $query = $this->newQuery();

        $query->select([
            'users.id',
            'users.code',
            'users.email',
            'users.status_id',
            'r.name as role',
            \DB::raw('CONCAT(users.first_name, " ", users.last_name) as name'),
            \DB::raw('IF(r.name = "' . Role::ROLE_PORTAL_ADMIN . '", "' . PortalHelper::name() . '", c.name) as company')
        ])
            ->join('model_has_roles as mhr', 'users.id', '=', 'mhr.model_id')
            ->join('roles as r', 'r.id', '=', 'mhr.role_id')
            ->leftJoin('companies as c', 'users.company_id', '=', 'c.id')
            ->whereIn('r.name', User::getManageableRoles());

        $query = $this->applySearch($query, $params['search']);

        return $query
            ->get()
            ->count();
    }
}
