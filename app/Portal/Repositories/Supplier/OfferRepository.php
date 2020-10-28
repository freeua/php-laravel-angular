<?php

namespace App\Portal\Repositories\Supplier;

use App\Helpers\PortalHelper;
use App\Portal\Helpers\AuthHelper;
use App\Portal\Models\Offer;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class OfferRepository
 *
 * @package App\Portal\Repositories\Supplier
 *
 * @method OfferRepository find(int $id, array $relations = [])
 */
class OfferRepository extends \App\Portal\Repositories\OfferRepository
{
    /** @var array */
    protected $filterWhereColumns = [
        'company' => 'c.name',
        'product' => 'pm.name',
        'date'    => ['IF(offers.status_updated_at, offers.status_updated_at, offers.created_at)', 'between', 'timestamps', '|'],
    ];
    /** @var array */
    protected $searchWhereColumns = [
        'number',
        'c.name',
        'pm.name',
        'CONCAT(u.first_name, " ", u.last_name)'
    ];

    /**
     * @inheritdoc
     */
    public function list(array $params, array $relationships = []): LengthAwarePaginator
    {
        $query = $this->newQuery()
            ->where('supplier_id', AuthHelper::supplierId())
            ->where('offers.status_id', '<>', Offer::STATUS_DRAFT);
        if (!empty($params['status_id'])) {
            if ($params['status_id'] == Offer::STATUS_REJECTED) {
                $query->where(function (Builder $query) {
                    $query->where(['offers.status_id' => Offer::STATUS_REJECTED])
                        ->orWhere(function (Builder $query) {
                            $query->whereNotIn('offers.status_id', [Offer::STATUS_PENDING])
                                ->whereDate('offers.expiry_date', '<=', Carbon::today());
                        });
                });
            } elseif ($params['status_id'] == Offer::STATUS_PENDING) {
                    $query->where('offers.expiry_date', '>', Carbon::today())
                        ->whereIn('offers.status_id', [Offer::STATUS_PENDING, Offer::STATUS_ACCEPTED, Offer::STATUS_PENDING_APPROVAL]);
            } elseif ($params['status_id'] == Offer::STATUS_CONTRACT_APPROVED) {
                $query
                    ->whereIn('offers.status_id', [Offer::STATUS_CONTRACT_APPROVED]);
            }
        } else {
            $query->where(function (Builder $query) {
                $query->whereIn('offers.status_id', [Offer::STATUS_PENDING, Offer::STATUS_PENDING_APPROVAL, Offer::STATUS_ACCEPTED, Offer::STATUS_CONTRACT_APPROVED])
                    ->orWhere(function (Builder $query) {
                        $query->whereNotIn('offers.status_id', [Offer::STATUS_ACCEPTED, Offer::STATUS_CONTRACT_APPROVED])
                            ->whereDate('offers.expiry_date', '>', Carbon::today());
                    });
            });
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
            'offers.id'
        ])
            ->join('portal_users as u', 'u.id', '=', 'offers.user_id')
            ->join('products as p', 'p.id', '=', 'offers.product_id')
            ->join('product_models as pm', 'pm.id', '=', 'p.model_id')
            ->join('companies as c', 'c.id', '=', 'u.company_id');

        $query->where('offers.portal_id', '=', PortalHelper::id());
        $query->where('offers.supplier_id', '=', AuthHelper::supplierId());
        $query = $this->applySearch($query, $params['search']);

        return $query
            ->count();
    }

    /**
     * @param array $params
     * @param bool  $groupByWeek
     *
     * @return Collection
     */
    public function getHistoryCount(array $params, bool $groupByWeek = false): Collection
    {
        $query = $this->newQuery();

        $dateField = $groupByWeek
            ? DB::raw('DATE(DATE_ADD(offers.status_updated_at, INTERVAL(-WEEKDAY(offers.status_updated_at)) DAY)) as week')
            : DB::raw('DATE(offers.status_updated_at) as date');

        $query->select([$dateField, DB::raw('COUNT(offers.id) as total')])
            ->where('offers.status_id', $params['status_id']);

        $query
            ->whereBetween(DB::raw('DATE(offers.status_updated_at)'), [$params['date_from'], $params['date_to']]);

        $query->where('offers.portal_id', '=', PortalHelper::id());
        $query->where('offers.supplier_id', '=', AuthHelper::supplierId());
        if ($groupByWeek) {
            $query->groupBy(DB::raw('week'));
        } else {
            $query->groupBy('date');
        }

        return $query->get();
    }
}
