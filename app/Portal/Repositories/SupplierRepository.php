<?php

namespace App\Portal\Repositories;

use App\Helpers\PortalHelper;
use App\Portal\Helpers\AuthHelper;
use App\Repositories\BaseRepository;
use App\Portal\Models\Supplier;
use App\Models\Portal;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use \Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Class SupplierRepository
 *
 * @package App\Portal\Repositories
 *
 * @method Supplier find(int $id, array $relations = [])
 */
class SupplierRepository extends BaseRepository
{
    /** @var array */
    protected $filterWhereColumns = [
        'city' => 'c.name',
        'status_id' => 'suppliers.status_id',
    ];
    /** @var array */
    protected $filterHavingColumns = [
        'number_of_products' => 'COUNT(p.id)',
    ];
    /** @var array */
    protected $searchWhereColumns = [
        'suppliers.name',
        'suppliers.status_id',
        'c.name'
    ];

    /**
     * SupplierRepository constructor.
     *
     * @param Supplier $supplier
     */
    public function __construct(Supplier $supplier)
    {
        $this->model = $supplier;
    }

    /**
     * @param array $data
     *
     * @return Supplier|false
     */
    public function create(array $data, Portal $portal)
    {
        $supplier = $this->model->newInstance();

        $supplier->name = $data['name'];
        $supplier->vat = $data['vat'];
        $supplier->admin_first_name = $data['admin_first_name'];
        $supplier->admin_last_name = $data['admin_last_name'];
        $supplier->admin_email = $data['admin_email'];
        $supplier->zip = $data['zip'] ?? null;
        $supplier->city_id = $data['city_id'];
        $supplier->phone = $data['phone'];
        $supplier->address = $data['address'];
        $supplier->gp_number = $data['gp_number'];
        $supplier->bank_account = $data['bank_account'];
        $supplier->bank_name = $data['bank_name'];
        $supplier->grefo = $data['grefo'];
        $supplier->status_id = Supplier::STATUS_ACTIVE;

        if ($supplier->save()) {
            $supplier->portals()->save($portal, [
                'status_id' => $data['status_id'],
                'blind_discount' => $data['blind_discount']
            ]);
            return $supplier;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function companyList(array $params, array $relationships = []): LengthAwarePaginator
    {
        $query = $this->newQuery();

        $query->select([
            'suppliers.*'
        ])
            ->join('portal_supplier', 'portal_supplier.supplier_id', '=', 'suppliers.id')
            ->leftJoin('company_supplier', 'company_supplier.supplier_id', '=', 'suppliers.id')
            ->with(['status']);

        $query->where('portal_supplier.portal_id', '=', PortalHelper::id());
        $query->where('company_supplier.company_id', '=', AuthHelper::companyId());
        $query->whereNull('company_supplier.deleted_at');

        if (!empty($params['status_id'])) {
            $query->where(['suppliers.status_id' => $params['status_id']]);
        }

        return $this->processList($query, $params, $relationships);
    }

    /**
     * @inheritdoc
     */
    public function list(array $params, array $relationships = []): LengthAwarePaginator
    {
        $query = $this->newQuery();

        $query->select([
            'suppliers.id',
            'suppliers.code',
            'suppliers.name',
            'portal_supplier.status_id',
            'c.name as city_name'
        ])
            ->join('cities as c', 'c.id', '=', 'suppliers.city_id')
            ->join('portal_supplier', 'portal_supplier.supplier_id', '=', 'suppliers.id')
            ->with(['status'])
            ->where('portal_supplier.portal_id', '=', PortalHelper::id())
            ->where('suppliers.status_id', Supplier::STATUS_ACTIVE);

        if (!empty($params['status_id'])) {
            $query->where(['suppliers.status_id' => $params['status_id']]);
        }

        return $this->processList($query, $params, $relationships);
    }

    public function allFiltered(array $params): Collection
    {
        $query = PortalHelper::getPortal()->suppliers();

        if (isset($params['status_id'])) {
            $query->where('status_id', $params['status_id']);
        }

        return $query->orderBy('name', 'asc')->get();
    }

    /**
     * @inheritdoc
     */
    public function searchTotal(array $params): int
    {
        $query = $this->newQuery();

        $query->select([
            'suppliers.admin_email',
        ])
            ->join('portal_supplier', 'portal_supplier.supplier_id', '=', 'suppliers.id')
            ->join('cities as c', 'c.id', '=', 'suppliers.city_id');

        $query->where('portal_supplier.portal_id', '=', PortalHelper::id());
        $query = $this->applySearch($query, $params['search']);

        return $query->count();
    }

    /**
     * @param Supplier $supplier
     * @param array $companyIds
     *
     * @return int
     */
    public function detachCompanies(Supplier $supplier, array $companyIds = []): int
    {
        $updated = 0;

        if (!$companyIds) {
            $companyIds = $supplier->companies()->pluck('company_id')->toArray();
        }

        foreach ($companyIds as $companyId) {
            $updated += $supplier->companies()->updateExistingPivot($companyId, ['deleted_at' => Carbon::now()]);
        }

        return $updated;
    }

    /**
     * @param array $params
     * @param bool $groupByWeek
     *
     * @return Collection
     */
    public function getHistoryCreated(array $params, bool $groupByWeek = false): Collection
    {
        $query = $this->newQuery();
        if (isset($params['portal_id'])) {
            $portalId = $params['portal_id'];
        } else {
            $portalId = PortalHelper::id();
        }

        $dateField = $groupByWeek
            ? \DB::raw('DATE(DATE_ADD(suppliers.created_at, INTERVAL(-WEEKDAY(suppliers.created_at)) DAY)) as week')
            : \DB::raw('DATE(suppliers.created_at) as date');

        $query->select([$dateField, \DB::raw('COUNT(suppliers.id) as total')])
            ->whereBetween(\DB::raw('DATE(suppliers.created_at)'), [$params['date_from'], $params['date_to']])
            ->join('portal_supplier', 'portal_supplier.supplier_id', '=', 'suppliers.id');

        $query->where('portal_supplier.portal_id', '=', $portalId);
        if ($groupByWeek) {
            $query->groupBy(\DB::raw('week'));
        } else {
            $query->groupBy(\DB::raw('DATE(suppliers.created_at)'));
        }

        return $query->withTrashed()->get();
    }

    /**
     * @param array $params
     *
     * @return Collection
     */
    public function getHistoryProducts(array $params): Collection
    {
        $query = $this->newQuery();
        if (isset($params['portal_id'])) {
            $portalId = $params['portal_id'];
        } else {
            $portalId = PortalHelper::id();
        }

        $query->select(['suppliers.name as name', \DB::raw('COUNT(p.id) as total')])
            ->join('products as p', 'suppliers.id', '=', 'p.supplier_id')
            ->join('portal_supplier', 'portal_supplier.supplier_id', '=', 'suppliers.id')
            ->where(function (Builder $where) use ($params) {
                $where->whereBetween(\DB::raw('DATE(p.created_at)'), [$params['date_from'], $params['date_to']])
                    ->orWhereNull('p.created_at');
            })
            ->where('portal_id', $portalId)
            ->orderBy('total', 'desc')
            ->groupBy('suppliers.id');

        return $query->withTrashed()->get();
    }

    /**
     * @param array $params
     *
     * @return Collection
     */
    public function getHistoryAssignedToCompany(array $params): Collection
    {
        $query = $this->newQuery();
        if (isset($params['portal_id'])) {
            $portalId = $params['portal_id'];
        } else {
            $portalId = PortalHelper::id();
        }

        $query->select(['suppliers.name as name', \DB::raw('COUNT(cs.id) as total')])
            ->join('portal_supplier', 'portal_supplier.supplier_id', '=', 'suppliers.id')
            ->leftJoin('company_supplier as cs', 'cs.supplier_id', '=', 'suppliers.id')
            ->where(function (Builder $where) use ($params) {
                $where->whereBetween(\DB::raw('DATE(cs.created_at)'), [$params['date_from'], $params['date_to']])
                    ->orWhereNull('cs.created_at');
            })
            ->where('portal_supplier.portal_id', $portalId);

        $query->groupBy('suppliers.id')
            ->orderBy('total', 'desc');

        return $query->withTrashed()->get();
    }

    /**
     * @param array $params
     *
     * @return Collection
     */
    public function getOrdersPerCompany(array $params): Collection
    {
        $query = $this->newQuery();
        if (isset($params['portal_id'])) {
            $portalId = $params['portal_id'];
        } else {
            $portalId = PortalHelper::id();
        }

        $query->select(['suppliers.name as name', \DB::raw('COUNT(cs.id) as total')])
            ->join('portal_supplier', 'portal_supplier.supplier_id', '=', 'suppliers.id')
            ->leftJoin('company_supplier as cs', 'cs.supplier_id', '=', 'suppliers.id')
            ->where(function (Builder $where) use ($params) {
                $where->whereBetween(\DB::raw('DATE(cs.created_at)'), [$params['date_from'], $params['date_to']])
                    ->orWhereNull('cs.created_at');
            })
            ->orderBy('total', 'desc');
        $query->where('portal_supplier.portal_id', '=', $portalId);

        $query->groupBy('suppliers.id');

        return $query->withTrashed()->get();
    }
}
