<?php

namespace App\Portal\Repositories\Company;

use App\Exceptions\ForbiddenException;
use App\Models\Permission;
use App\Portal\Helpers\AuthHelper;
use App\Helpers\PortalHelper;
use App\Portal\Models\Role;
use App\Portal\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Class UserRepository
 *
 * @package App\Portal\Repositories\Company
 *
 * @method User find(int $id, array $relations = [])
 */
class UserRepository extends \App\Portal\Repositories\Base\UserRepository
{
    protected $filterWhereColumns = [
        'name' => 'name',
    ];
    protected $searchHavingColumns = [
        'code',
        'email',
        'role',
        'name',
    ];

    /**
     * @inheritdoc
     */
    public function list(array $params, array $relationships = ['status']): LengthAwarePaginator
    {
        $companyId = AuthHelper::companyId();
        $query = $this->newQuery();

        $query->select([
            'portal_users.id',
            'portal_users.code',
            'portal_users.email',
            'portal_users.created_at',
            'portal_users.status_id',
            \DB::raw('GROUP_CONCAT(r.label SEPARATOR \', \') as role'),
            \DB::raw('CONCAT(portal_users.first_name, " ", portal_users.last_name) as name'),
            \DB::raw('IF(rejected_by, "Admin Rejected", "") as additional_info'),
            'c.name AS company_name',
            'c.code AS company_code'
        ])
            ->join('model_has_roles as mhr', 'portal_users.id', '=', 'mhr.model_id')
            ->join('roles as r', 'r.id', '=', 'mhr.role_id')
            ->leftJoin('companies as c', 'portal_users.company_id', '=', 'c.id')
            ->whereIn('r.name', User::getManageableRoles())
            ->where(function (Builder $query) use ($companyId) {
                $query->whereIn('portal_users.company_id', function ($query) use ($companyId) {
                    $query->select('id')->from('companies')->where('parent_id', $companyId);
                })
                    ->orWhere('portal_users.company_id', $companyId);
            });
        $query->where('portal_users.portal_id', '=', PortalHelper::id());
        $query->groupBy('portal_users.id')
            ->orderBy('portal_users.company_id', 'asc');
        if (!empty($params['status_id'])) {
            if ($params['status_id'] == User::STATUS_PENDING) {
                if (AuthHelper::user()->hasPermissionTo(Permission::MANAGE_COMPANY_EMPLOYEES, Role::COMPANY_GUARD) === false) {
                    throw new ForbiddenException('This action is unauthorized.');
                }
            }
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

        $result = $items
            ->slice(($page - 1) * $perPage, $perPage)
            ->all();

        return new LengthAwarePaginator(array_values($result), count($items), $perPage);
    }

    /**
     * @inheritdoc
     */
    public function findAllEmployees(array $params, array $relationships = ['status']): LengthAwarePaginator
    {
        $companyId = AuthHelper::companyId();
        $query = $this->newQuery();
        $role = Role::ROLE_EMPLOYEE;
        $query->select([
            'portal_users.id',
            'portal_users.code',
            'portal_users.email',
            'portal_users.created_at',
            'portal_users.status_id',
            \DB::raw('GROUP_CONCAT(r.label SEPARATOR \', \') as role'),
            \DB::raw('CONCAT(portal_users.first_name, " ", portal_users.last_name) as name'),
            \DB::raw('IF(rejected_by, "Admin Rejected", "") as additional_info'),
            'c.name AS company_name',
            'c.code AS company_code'
        ])
            ->join('model_has_roles as mhr', 'portal_users.id', '=', 'mhr.model_id')
            ->join('roles as r', 'r.id', '=', 'mhr.role_id')
            ->leftJoin('companies as c', 'portal_users.company_id', '=', 'c.id')
            ->whereIn('portal_users.company_id', function ($query) use ($companyId) {
                $query->select('id')->from('companies')->where('parent_id', $companyId);
            })
            ->orWhere('portal_users.company_id', $companyId)
            ->groupBy('portal_users.id');
        $query->where('portal_users.portal_id', '=', PortalHelper::id());
        $query->where('portal_users.company_id', '=', $companyId);
        $query->where('r.name', '=', $role);
        $query->orderBy('portal_users.company_id', 'asc');
        if (!empty($params['status_id'])) {
            if ($params['status_id'] == User::STATUS_PENDING) {
                if (AuthHelper::user()->hasPermissionTo(Permission::MANAGE_COMPANY_EMPLOYEES, Role::COMPANY_GUARD) === false) {
                    throw new ForbiddenException('This action is unauthorized.');
                }
            }
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

        $result = $items
            ->slice(($page - 1) * $perPage, $perPage)
            ->all();

        return new LengthAwarePaginator(array_values($result), count($items), $perPage);
    }

    /**
     * @inheritdoc
     */
    public function searchTotal(array $params): int
    {
        $query = $this->newQuery();

        $query->select([
            'portal_users.code',
            'portal_users.email',
            'r.name as role',
            \DB::raw('CONCAT(portal_users.first_name, " ", portal_users.last_name) as name'),
        ])
            ->join('model_has_roles as mhr', 'portal_users.id', '=', 'mhr.model_id')
            ->join('roles as r', 'r.id', '=', 'mhr.role_id')
            ->leftJoin('companies as c', 'portal_users.company_id', '=', 'c.id')
            ->whereIn('r.name', User::getManageableRoles())
            ->where('portal_users.company_id', AuthHelper::companyId());
        $query->where('portal_users.portal_id', '=', PortalHelper::id());
        $query = $this->applySearch($query, $params['search']);

        return $query
            ->get()
            ->count();
    }
}
