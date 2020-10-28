<?php

namespace App\Portal\Repositories\Company;

use App\Portal\Helpers\AuthHelper;
use App\Helpers\PortalHelper;
use App\Portal\Models\Offer;
use App\Portal\Models\Supplier;
use App\Portal\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class OfferRepository
 *
 * @package App\Portal\Repositories\Company
 *
 * @method OfferRepository find(int $id, array $relations = [])
 */
class OfferRepository extends \App\Portal\Repositories\OfferRepository
{
    /** @var array */
    protected $filterWhereColumns = [
        'supplier' => 's.name',
        'product'  => 'pm.name'
    ];
    /** @var array */
    protected $searchWhereColumns = [
        'number',
        's.name',
        'pm.name',
        'CONCAT(u.first_name, " ", u.last_name)'
    ];

    /**
     * @inheritdoc
     */
    public function list(array $params, array $relationships = ['status']): LengthAwarePaginator
    {
        $companyId = AuthHelper::companyId();
        $query = $this->newQuery();

        $query->where(function (Builder $q) use ($companyId) {
            $q->whereIn('offers.company_id', function ($query) use ($companyId) {
                $query->select('id')->from('companies')->where('parent_id', $companyId);
            })
                ->orWhere('offers.company_id', $companyId);
        });

        if (!empty($params['status_id'])) {
            if ($params['status_id'] == Offer::STATUS_REJECTED) {
                $query->where(function (Builder $q) {
                    $q->where(['offers.status_id' => Offer::STATUS_REJECTED])
                        ->orWhere(function (Builder $q2) {
                            $q2->whereNotIn('offers.status_id', [Offer::STATUS_ACCEPTED, Offer::STATUS_CONTRACT_APPROVED])
                                ->where('offers.expiry_date', '<=', Carbon::today());
                        });
                });
            } elseif ($params['status_id'] == Offer::STATUS_PENDING) {
                $query->where('offers.expiry_date', '>', Carbon::today())
                    ->where('offers.status_id', Offer::STATUS_PENDING);
            } else {
                $query->where(['offers.status_id' => $params['status_id']]);
            }
        } else {
            $query->where(function (Builder $query) {
                $query->whereIn('offers.status_id', [Offer::STATUS_ACCEPTED, Offer::STATUS_CONTRACT_APPROVED])
                    ->orWhere(function (Builder $query) {
                        $query->whereNotIn('offers.status_id', [Offer::STATUS_ACCEPTED, Offer::STATUS_CONTRACT_APPROVED])
                            ->whereDate('offers.expiry_date', '>', Carbon::today());
                    });
            });
        }

        $params['order_by'] = $params['order_by'] ?? 'offers.id';

        return $this->processList($query, $params, $relationships);
    }

    /**
     * @param User $user
     * @param array $params
     * @param array $relationships
     * @return LengthAwarePaginator
     */
    public function userList(User $user, array $params, array $relationships = ['status']): LengthAwarePaginator
    {
        $query = $this->newQuery();

        $query
            ->where('offers.company_id', AuthHelper::companyId())
            ->where('offers.user_id', $user->id);

        if (!empty($params['status_id'])) {
            if ($params['status_id'] == Offer::STATUS_REJECTED) {
                $query->where(['offers.status_id' => Offer::STATUS_REJECTED])
                    ->orWhere(function (Builder $query) {
                        $query->whereNotIn('offers.status_id', [Offer::STATUS_ACCEPTED, Offer::STATUS_CONTRACT_APPROVED])
                            ->where('offers.expiry_date', '<=', Carbon::today());
                    });
            } elseif ($params['status_id'] == Offer::STATUS_PENDING) {
                $query->where('offers.expiry_date', '>', Carbon::today())
                    ->where('offers.status_id', Offer::STATUS_PENDING);
            } else {
                $query->where(['offers.status_id' => $params['status_id']]);
            }
        }

        $params['order_by'] = $params['order_by'] ?? 'offers.id';

        return $this->processList($query, $params, $relationships);
    }

    /**
     * @param Supplier $supplier
     * @param array $params
     * @param array $relationships
     * @return LengthAwarePaginator
     */
    public function suppliersList(Supplier $supplier, array $params, array $relationships = ['status']): LengthAwarePaginator
    {
        $query = $this->newQuery();

        $query
            ->where('offers.company_id', AuthHelper::companyId())
            ->where('offers.supplier_id', $supplier->id);

        if (!empty($params['status_id'])) {
            if ($params['status_id'] == Offer::STATUS_REJECTED) {
                $query->where(function (Builder $query) {
                    $query->where(['offers.status_id' => Offer::STATUS_REJECTED])
                        ->orWhere(function (Builder $query) {
                            $query->whereNotIn('offers.status_id', [Offer::STATUS_ACCEPTED, Offer::STATUS_CONTRACT_APPROVED])
                                ->whereDate('offers.expiry_date', '<=', Carbon::today());
                        });
                });
            } elseif ($params['status_id'] == Offer::STATUS_PENDING) {
                $query->where('offers.expiry_date', '>', Carbon::today())
                    ->where('offers.status_id', Offer::STATUS_PENDING);
            } else {
                $query->where(['offers.status_id' => $params['status_id']]);
            }
        } else {
            $query->where(function (Builder $query) {
                $query->whereIn('offers.status_id', [Offer::STATUS_ACCEPTED, Offer::STATUS_CONTRACT_APPROVED])
                    ->orWhere(function (Builder $query) {
                        $query->whereNotIn('offers.status_id', [Offer::STATUS_ACCEPTED, Offer::STATUS_CONTRACT_APPROVED])
                            ->whereDate('offers.expiry_date', '>', Carbon::today());
                    });
            });
        }

        $params['order_by'] = $params['order_by'] ?? 'offers.id';

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
            ->join('portal_users as u', function ($join) {
                $join->on('u.id', '=', 'offers.user_id')->where('u.company_id', AuthHelper::companyId());
            })
            ->join('products as p', 'p.id', '=', 'offers.product_id')
            ->join('product_models as pm', 'pm.id', '=', 'p.model_id')
            ->join('suppliers as s', 'offers.supplier_id', '=', 's.id');
        $query->where('offers.portal_id', '=', PortalHelper::id());
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
            ->join('portal_users as u', 'u.id', '=', 'offers.user_id')
            ->where('u.company_id', AuthHelper::companyId())
            ->whereIn('offers.status_id', $params['status_id'])
            ->whereBetween('offers.status_updated_at', [$params['date_from'], $params['date_to']]);
        $query->where('offers.portal_id', '=', PortalHelper::id());
        if ($groupByWeek) {
            $query->groupBy(DB::raw('week'));
        } else {
            $query->groupBy('date');
        }

        return $query->get();
    }
}
