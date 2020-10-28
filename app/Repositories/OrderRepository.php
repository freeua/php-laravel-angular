<?php

namespace App\Repositories;

use App\Helpers\PortalHelper;
use App\Models\Portal;
use App\Portal\Models\Order;
use DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Class OrderRepository
 *
 * @package App\System\Repositories
 * @method Order find(int $id, array $relations = [])
 */
class OrderRepository extends BaseRepository
{

    /** @var array */
    protected $filterWhereColumns = [
        'supplier' => 's.name',
        'company' => 'orders.company_name',
        'product' => 'orders.product_name'
    ];
    /** @var array */
    protected $searchWhereColumns = [
        's.name',
        'p.name',
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
        $query = $this->newQuery()->with(['portal', 'supplier']);

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

        $query->select(['orders.*', 'p.name as portal_name', 's.name as supplier_name'])
            ->join('portals as p', 'p.id', '=', 'orders.portal_id')
            ->join('suppliers as s', 's.id', '=', 'orders.supplier_id');

        $query = $this->applySearch($query, $params['search']);

        return $query->count();
    }

    /**
     * Returns list of orders for export
     *
     * @param array $params
     *
     * @return Collection
     */
    public function exportList(array $params): Collection
    {
        return $this
            ->list($params)->map(function ($item) {
                return $item->only(['number', 'leasing_period', 'username', 'company_name', 'product_name']);
            });
    }

    /**
     * @param int $supplierId
     * @param string|null $status
     * @param int $limit
     *
     * @return Collection
     */
    public function getPerSupplierCompanyCount(int $supplierId, int $limit, ?string $status = null): Collection
    {
        $query = $this->newQuery();

        $query->select(['c.name as name', 'c.id as id', DB::raw('COUNT(orders.id) as total')])
            ->join('companies as c', 'c.id', '=', 'orders.company_id')
            ->where('orders.portal_id', PortalHelper::id())
            ->where('orders.supplier_id', $supplierId);

        if ($status) {
            $query->where('orders.status_id', $status);
        }

        $query->groupBy('c.id')
            ->orderBy('total', 'desc')
            ->limit($limit);

        return $query->get();
    }

    /**
     * @param array $params
     * @param Portal|null $portal
     *
     * @return Collection
     */
    public function getPerCompanyHistoryCount(array $params): Collection
    {
        $query = $this->newQuery();

        if (isset($params['portal_id'])) {
            $portalId = $params['portal_id'];
        } else {
            $portalId = PortalHelper::id();
        }

        $query->select(['c.name as name', DB::raw('COUNT(orders.id) as total')])
            ->join('companies as c', 'c.id', '=', 'orders.company_id')
            ->where('orders.portal_id', $portalId)
            ->whereBetween(DB::raw('DATE(date)'), [$params['date_from'], $params['date_to']])
            ->groupBy('c.id')
            ->orderBy('total', 'desc');
        $query->where('orders.portal_id', $portalId);
        if (!empty($params['supplier_id'])) {
            $query->where('supplier_id', $params['supplier_id']);
        }

        return $query->withTrashed()->get();
    }

    /**
     * @param int $portalId
     * @param array $params
     * @param bool $groupByWeek
     *
     * @return Collection
     */
    public function getHistoryCount(int $portalId, array $params, bool $groupByWeek = false): Collection
    {
        $query = $this->newQuery();

        $dateField = $groupByWeek
            ? DB::raw('DATE(DATE_ADD(date, INTERVAL(-WEEKDAY(date)) DAY)) as week')
            : 'date';

        $query->select([$dateField, DB::raw('COUNT(orders.id) as total')])
            ->where('portal_id', $portalId)
            ->whereBetween(DB::raw('DATE(date)'), [$params['date_from'], $params['date_to']]);

        if (!empty($params['company_id'])) {
            $query->where('company_id', $params['company_id']);
        }

        if ($groupByWeek) {
            $query->groupBy(DB::raw('week'));
        } else {
            $query->groupBy('date');
        }

        return $query->withTrashed()->get();
    }

    /**
     * @param int $portalId
     * @param int $companyId
     * @param array $params
     *
     * @return Collection
     */
    public function getCompanyStatusCount(int $portalId, int $companyId, array $params): Collection
    {
        $query = $this->newQuery();

        $query->select([DB::raw('COUNT(orders.id) as total'), DB::raw('statuses.label status')])
            ->join('statuses', 'orders.status_id', 'statuses.id')
            ->where('portal_id', $portalId)
            ->where('company_id', $companyId)
            ->whereBetween(DB::raw('DATE(orders.created_at)'), [$params['date_from'], $params['date_to']]);

        $query->groupBy('status')
            ->orderBy('status');

        /** @var Collection $result */
        $result = $query->withTrashed()->get();

        $result = $result->keyBy('status');

        $statuses = Order::getStatuses();

        foreach ($statuses as $status) {
            if (!$result->has($status->label)) {
                $result->push(['total' => 0, 'status' => $status->label]);
            }
        }

        return $result->values();
    }

    /**
     * @return string
     */
    public function generatePickupCode(): string
    {
        $letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $code = '';
        $lettersLength = strlen($letters);

        for ($i = 0; $i < Order::PICKUP_CODE_LETTERS_COUNT; $i++) {
            $code .= $letters[rand(0, $lettersLength - 1)];
        }

        $code .= str_pad(rand(1, 999), Order::PICKUP_CODE_DIGITS_COUNT, 0);

        return str_shuffle($code);
    }

    protected function applySearch(Builder $query, string $search): Builder
    {
        return $query
            ->where(function (Builder $query) use ($search) {
                return $query
                    ->orWhereHas('company', function (Builder $query) use ($search) {
                        return $query->where('name', 'like', "%$search%");
                    })
                    ->orWhereHas('product.brand', function (Builder $query) use ($search) {
                        return $query->where('name', 'like', "%$search%");
                    })
                    ->orWhereHas('product.model', function (Builder $query) use ($search) {
                        return $query->where('name', 'like', "%$search%");
                    })
                    ->orWhereHas('user', function (Builder $query) use ($search) {
                        return $query
                            ->where('first_name', 'like', "%$search%")
                            ->orWhere('last_name', 'like', "%$search%");
                    })
                    ->orWhere('number', 'like', "%$search%");
            });
    }
}
