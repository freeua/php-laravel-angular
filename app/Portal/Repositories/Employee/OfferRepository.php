<?php

namespace App\Portal\Repositories\Employee;

use App\Helpers\PortalHelper;
use App\Portal\Helpers\AuthHelper;
use App\Portal\Models\Offer;
use DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Carbon;

/**
 * Class OfferRepository
 *
 * @package App\Portal\Repositories\Employee
 *
 * @method OfferRepository find(int $id, array $relations = [])
 */
class OfferRepository extends \App\Portal\Repositories\OfferRepository
{
    /** @var array */
    protected $filterWhereColumns = [
        'supplier' => 'suppliers.name',
        'product' => 'product_models.name'
    ];
    /** @var array */
    protected $searchWhereColumns = [
        'number',
        'suppliers.name',
        'product_models.name'
    ];

    /**
     * @inheritdoc
     */
    public function list(array $params, array $relationships = []): LengthAwarePaginator
    {
        $query = $this->newQuery();
        $query
            ->where('offers.user_id', AuthHelper::id())
            ->where('offers.portal_id', '=', PortalHelper::id());

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

        $result = $this->processList($query, $params, $relationships);

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function searchTotal(array $params
    ): int
    {
        $query = $this->newQuery();

        $query->select([
            'offers.id'
        ])
            ->join('suppliers as s', 'offers.supplier_id', '=', 's.id')
            ->join('products as p', 'p.id', '=', 'offers.product_id')
            ->join('product_models as pm', 'pm.id', '=', 'p.model_id')
            ->join('product_brands as b', 'b.id', '=', 'p.brand_id')
            ->where('offers.user_id', AuthHelper::id())
            ->whereIn('s.id', AuthHelper::relatedSupplierIds());
        $query->where('offers.portal_id', '=', PortalHelper::id());
        $query = $this->applySearch($query, $params['search']);

        return $query
            ->count();
    }
}
