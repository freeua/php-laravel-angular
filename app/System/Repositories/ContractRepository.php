<?php

namespace App\System\Repositories;

use App\Helpers\PortalHelper;
use App\Repositories\BaseRepository;
use App\Portal\Models\Contract;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class ContractRepository
 *
 * @package App\System\Repositories
 * @method Contract find(int $id, array $relations = [])
 */
class ContractRepository extends BaseRepository
{
    /** @var array */
    protected $filterWhereColumns = [
        'portal'     => 'p.name',
        'product'    => 'product_name',
        'start_date' => ['start_date', 'between', 'timestamps', '|'],
        'end_date'   => ['end_date', 'between', 'timestamps', '|'],
    ];
    /** @var array */
    protected $searchWhereColumns = [
        'contracts.username',
        'p.name'
    ];


    /**
     * @var PortalRepository
     */
    private $portalRepository;

    /**
     * ContractRepository constructor.
     *
     * @param Contract                $contract
     * @param PortalRepository $portalRepository
     */
    public function __construct(
        Contract $contract,
        PortalRepository $portalRepository
    ) {
        $this->model = $contract;
        $this->portalRepository = $portalRepository;
    }

    /**
     * @param array $data
     *
     * @return Contract|false
     */
    public function create(array $data)
    {
        $contract = $this->model->newInstance();

        $contract->order_id = $data['order_id'];
        $contract->portal_id = $data['portal_id'];
        $contract->user_id = $data['user_id'];
        $contract->product_id = $data['product_id'];
        $contract->supplier_id = $data['supplier_id'];
        $contract->company_id = $data['company_id'];
        $contract->username = $data['username'];
        $contract->productModel = $data['product_name'];
        $contract->statusId = $data['status_id'];
        $contract->agreedPurchasePrice = $data['agreed_purchase_price'];
        $contract->productListPrice = $data['bike_list_price'];
        $contract->productDiscountedPrice = $data['bike_discounted_price'];
        $contract->productDiscount = $data['bike_discount'];
        $contract->accessoriesPrice = $data['accessories_price'];
        $contract->accessoriesDiscountedPrice = $data['accessories_discounted_price'];
        $contract->leasingRate = $data['leasing_rate'];
        $contract->insuranceRateName = $data['insurance_rate_name'];
        $contract->serviceRateName = $data['service_rate_name'];
        $contract->insuranceRate = $data['insurance_rate'];
        $contract->serviceRate = $data['service_rate'];
        $contract->leasingRateSubsidy = $data['leasing_rate_subsidy'];
        $contract->insuranceRateSubsidy = $data['insurance_rate_subsidy'];
        $contract->serviceRateSubsidy = $data['service_rate_subsidy'];
        $contract->calculatedResidualValue = $data['calculated_residual_value'];
        $contract->leasingPeriod = $data['leasing_period'];
        $contract->productSize = $data['product_size'];
        $contract->start_date = $data['start_date'];
        $contract->end_date = $data['end_date'];
        $contract->notes = $data['notes'];
        $contract->cancellation_reason = $data['cancellation_reason'];

        if (!$contract->save()) {
            return false;
        }

        $contract->number = str_pad($contract->id, 6, '0', STR_PAD_LEFT);

        return $contract->save() ? $contract : false;
    }

    /**
     * @inheritdoc
     */
    public function list(array $params, array $relationships = []): LengthAwarePaginator
    {
        $query = $this->newQuery()
        ->with(['portal']);

        if (!empty($params['status_id']) && $params['status_id'] == Contract::STATUS_CANCELED) {
            $query->where(['contracts.status_id' => $params['status_id']]);
        } elseif (!empty($params['status_id'])) {
            $query->where('contracts.status_id', '!=', Contract::STATUS_CANCELED);
            if ($params['status_id'] == Contract::STATUS_ACTIVE) {
                $query->where('contracts.start_date', '<', Carbon::now()->toDateString());
                $query->where('contracts.end_date', '>', Carbon::now()->toDateString());
            } else {
                $query->where('contracts.start_date', '>', Carbon::now()->toDateString());
                $query->orWhere('contracts.end_date', '<', Carbon::now()->toDateString());
            }
        }

        return $this->processList($query, $params, $relationships);
    }

    /**
     * @inheritdoc
     */
    public function listForPortal(array $params, array $relationships = []): LengthAwarePaginator
    {
        $query = $this->newQuery()
        ->with(['portal']);

        $query->where(['contracts.portal_id' => PortalHelper::id()]);

        if (!empty($params['status_id']) && $params['status_id'] == Contract::STATUS_CANCELED) {
            $query->where(['contracts.status_id' => $params['status_id']]);
        } elseif (!empty($params['status_id'])) {
            $query->where('contracts.status_id', '!=', Contract::STATUS_CANCELED);
            if ($params['status_id'] == Contract::STATUS_ACTIVE) {
                $query->where('contracts.start_date', '<', Carbon::now()->toDateString());
                $query->where('contracts.end_date', '>', Carbon::now()->toDateString());
            } else {
                $query->where('contracts.start_date', '>', Carbon::now()->toDateString());
                $query->orWhere('contracts.end_date', '<', Carbon::now()->toDateString());
            }
        }

        return $this->processList($query, $params, $relationships);
    }

    /**
     * @inheritdoc
     */
    public function searchTotal(array $params): int
    {
        $query = $this->newQuery();

        $query->select(['contracts.*', 'p.name as portal_name'])
            ->join('portals as p', 'p.id', '=', 'contracts.portal_id');

        $query = $this->applySearch($query, $params['search']);

        return $query->count();
    }

    /**
     * Returns list of contracts for export
     *
     * @param array $params
     *
     * @return Collection
     */
    public function exportList(array $params): Collection
    {
        return $this
            ->list($params)->map(function ($item) {
                return $item->only(['number', 'start_date', 'end_date', 'username', 'portal_name', 'product_name']);
            });
    }

    /**
     * @param int $id
     *
     * @return Contract|null
     */
    public function getContract(int $id): ?Contract
    {
        return $this->find($id);
    }

    /**
     * @param int   $portalId
     * @param array $params
     * @param bool  $groupByWeek
     *
     * @return Collection
     */
    public function getHistoryCreated(int $portalId, array $params, bool $groupByWeek = null): Collection
    {
        $query = $this->newQuery();

        $dateField = $groupByWeek
            ? DB::raw('DATE(DATE_ADD(created_at, INTERVAL(-WEEKDAY(created_at)) DAY)) as week')
            : DB::raw('DATE(created_at) as date');

        $query->select([$dateField, DB::raw('COUNT(contracts.id) as total')])
            ->where('portal_id', $portalId)
            ->whereBetween(DB::raw('DATE(created_at)'), [$params['date_from'], $params['date_to']]);

        if (!empty($params['company_id'])) {
            $query->where('company_id', $params['company_id']);
        }

        if ($groupByWeek) {
            $query->groupBy(DB::raw('week'));
        } else {
            $query->groupBy(DB::raw('DATE(created_at)'));
        }

        return $query->withTrashed()->get();
    }

    /**
     * @param int   $portalId
     * @param int   $companyId
     * @param array $params
     *
     * @return Collection
     */
    public function getCompanyPerEmployeeHistoryCount(int $portalId, int $companyId, array $params): Collection
    {
        $query = $this->newQuery();

        $portal = $this->portalRepository->find($portalId);

        $query->select([DB::raw('CONCAT(u.first_name, " ", u.last_name) as name'), DB::raw('COUNT(contracts.id) as total')])
            ->join('portal_users as u', 'u.id', '=', 'contracts.user_id')
            ->where('contracts.portal_id', $portal->id)
            ->where('contracts.company_id', $companyId)
            ->whereBetween(DB::raw('DATE(contracts.created_at)'), [$params['date_from'], $params['date_to']]);

        if (!empty($params['status'])) {
            $query->where('contracts.status', $params['status']);
        }

        $query->groupBy('u.id')
            ->orderBy('name');

        return $query->withTrashed()->get();
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
