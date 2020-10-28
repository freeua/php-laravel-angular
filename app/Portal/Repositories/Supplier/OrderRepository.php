<?php

namespace App\Portal\Repositories\Supplier;

use App\Portal\Helpers\AuthHelper;
use App\Helpers\PortalHelper;
use App\Repositories\BaseOrderRepository;
use App\Portal\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class OrderRepository
 *
 * @package App\Portal\Repositories\Supplier
 * @method Order find(int $id, array $relations = [])
 */
class OrderRepository extends BaseOrderRepository
{
    /** @var array */
    protected $filterWhereColumns = [
        'company' => 'company_name',
        'product' => 'product_name',
        'username' => 'username'
    ];
    /** @var array */
    protected $searchWhereColumns = [
        'number',
        'c.name',
        'username',
        'product_name'
    ];

    /**
     * @inheritdoc
     */
    public function list(array $params, array $relationships = []): LengthAwarePaginator
    {
        $query = $this->newQuery();

        $query->where('orders.supplier_id', AuthHelper::supplierId());

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
            ->join('companies as c', 'c.id', '=', 'orders.company_id')
            ->where('orders.portal_id', PortalHelper::id())
            ->where('orders.supplier_id', AuthHelper::supplierId());

        $query->where('orders.portal_id', '=', PortalHelper::id());
        $query = $this->applySearch($query, $params['search']);

        return $query->count();
    }
}
