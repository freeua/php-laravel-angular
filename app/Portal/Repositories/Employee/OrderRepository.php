<?php

namespace App\Portal\Repositories\Employee;

use App\Portal\Helpers\AuthHelper;
use App\Helpers\PortalHelper;
use App\Repositories\BaseOrderRepository;
use App\Portal\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Class OrderRepository
 *
 * @package App\Portal\Repositories\Employee
 * @method Order find(int $id, array $relations = [])
 */
class OrderRepository extends BaseOrderRepository
{
    /** @var array */
    protected $filterWhereColumns = [
        'supplier' => 's.name',
        'product'  => 'product_name'
    ];
    /** @var array */
    protected $searchWhereColumns = [
        'number',
        's.name',
        'username',
        'product_name'
    ];

    /**
     * @inheritdoc
     */
    public function list(array $params, array $relationships = []): LengthAwarePaginator
    {
        $query = $this->newQuery()
            ->where('orders.portal_id', PortalHelper::id())
            ->where('orders.user_id', AuthHelper::id())
        ->with(['supplier']);
        if (!empty($params['status_id'])) {
            $query->where(['orders.status_id' => $params['status_id']]);
        }

        return $this->processList($query, $params, $relationships);
    }

    /**
     * @inheritdoc
     */
    public function searchTotal(array $params): int
    {
        $query = $this->newQuery();

        $query->select([
            'orders.id'
        ])
            ->join('suppliers as s', 's.id', '=', 'orders.supplier_id')
            ->where('orders.portal_id', PortalHelper::id())
            ->where('orders.user_id', AuthHelper::id());

        $query = $this->applySearch($query, $params['search']);

        return $query->count();
    }
}
