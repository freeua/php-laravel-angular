<?php

namespace App\Portal\Repositories\Supplier;

use App\Portal\Helpers\AuthHelper;
use App\Helpers\PortalHelper;
use App\Portal\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Class UserRepository
 *
 * @package App\Portal\Repositories\Supplier
 *
 * @method User find(int $id, array $relations = [])
 */
class UserRepository extends \App\Portal\Repositories\Base\UserRepository
{
    protected $searchHavingColumns = [
        'code',
        'email',
        'name',
    ];

    /**
     * @inheritdoc
     */
    public function list(array $params, array $relationships = []): LengthAwarePaginator
    {
        $query = $this->newQuery();

        $query->select([
            'portal_users.id',
            'portal_users.code',
            'portal_users.email',
            'portal_users.status_id',
            \DB::raw('CONCAT(portal_users.first_name, " ", portal_users.last_name) as name')
        ])
            ->join('model_has_roles as mhr', 'portal_users.id', '=', 'mhr.model_id')
            ->join('roles as r', 'r.id', '=', 'mhr.role_id')
            ->whereIn('r.name', User::getManageableRoles())
            ->where('portal_users.supplier_id', AuthHelper::supplierId());

        if (!empty($params['status_id'])) {
            $query->where(['portal_users.status_id' => $params['status_id']]);
        }

        $query->where('portal_users.portal_id', '=', PortalHelper::id());
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
            \DB::raw('CONCAT(portal_users.first_name, " ", portal_users.last_name) as name'),
        ])
            ->join('model_has_roles as mhr', 'portal_users.id', '=', 'mhr.model_id')
            ->join('roles as r', 'r.id', '=', 'mhr.role_id')
            ->whereIn('r.name', User::getManageableRoles())
            ->where('portal_users.supplier_id', AuthHelper::supplierId());

        $query->where('portal_users.portal_id', '=', PortalHelper::id());
        $query = $this->applySearch($query, $params['search']);

        return $query
            ->get()
            ->count();
    }
}
