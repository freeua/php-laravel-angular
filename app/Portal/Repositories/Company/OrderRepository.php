<?php

namespace App\Portal\Repositories\Company;

use App\Portal\Helpers\AuthHelper;
use App\Helpers\PortalHelper;
use App\Portal\Models\Order;
use App\Repositories\BaseOrderRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class OrderRepository
 *
 * @package App\Portal\Repositories\Company
 * @method Order find(int $id, array $relations = [])
 */
class OrderRepository extends BaseOrderRepository
{
    /** @var array */
    protected $filterWhereColumns = [
        'supplier' => 's.name',
        'product' => 'product_name'
    ];
    /** @var array */
    protected $searchWhereColumns = [
        'number',
        's.name',
        'username',
        'product_name'
    ];

    /** @var Order */
    protected $model;

    /**
     * BaseOrderRepository constructor.
     *
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->model = $order;
    }

    /**
     * @inheritdoc
     */
    public function list(array $params, array $relationships = []): LengthAwarePaginator
    {
        $companyId = AuthHelper::companyId();
        $query = $this->newQuery()
            ->where('orders.portal_id', PortalHelper::id())
            ->where(function (Builder $q) use ($companyId) {
                $q->whereIn('orders.company_id', function ($query) use ($companyId) {
                    $query->select('id')->from('companies')->where('parent_id', $companyId);
                })
                    ->orWhere('orders.company_id', $companyId);
            })
            ->with(['supplier', 'user', 'product']);
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
            ->where('orders.company_id', AuthHelper::companyId());

        $query = $this->applySearch($query, $params['search']);

        return $query->count();
    }

    /**
     * @param array $params
     * @param bool $groupByWeek
     *
     * @return Collection
     */
    public function getHistoryCount(array $params, bool $groupByWeek = false): Collection
    {
        $query = $this->newQuery();

        $dateField = $groupByWeek
            ? DB::raw('DATE(DATE_ADD(date, INTERVAL(-WEEKDAY(date)) DAY)) as week')
            : 'date';

        $query->select([$dateField, DB::raw('COUNT(offers.id) as total')])
            ->where('company_id', AuthHelper::companyId())
            ->where('status_id', $params['status_id'])
            ->whereBetween(DB::raw('DATE(date)'), [$params['date_from'], $params['date_to']]);
        $query->where('orders.portal_id', '=', PortalHelper::id());
        if ($groupByWeek) {
            $query->groupBy(DB::raw('week'));
        } else {
            $query->groupBy('date');
        }

        return $query->withTrashed()->get();
    }
}
