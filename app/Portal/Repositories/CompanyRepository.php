<?php

namespace App\Portal\Repositories;

use App\Helpers\PortalHelper;
use App\Models\Companies\Company;
use App\Portal\Models\Role;
use App\Portal\Models\User;
use App\Repositories\BaseRepository;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class CompanyRepository
 *
 * @package App\Portal\Repositories
 *
 * @method Company find(int $id, array $relations = [])
 */
class CompanyRepository extends BaseRepository
{
    /** @var array */
    protected $filterWhereColumns = [
        'city' => 'cities.name',
        'status_id' => 'companies.status_id',
    ];
    /** @var array */
    protected $searchWhereColumns = [
        'companies.code',
        'companies.name',
        'companies.status_id',
        'cities.name'
    ];

    /**
     * CompanyRepository constructor.
     *
     * @param Company $company
     */
    public function __construct(Company $company)
    {
        $this->model = $company;
    }

    /**
     * @param array $data
     *
     * @return Company|false
     */
    public function create(array $data)
    {
        $model = $this->model->newInstance();

        $model->code = $this->generateCode($data['name']);
        $model->color = $data['color'];
        $model->name = $data['name'];
        $model->slug = $data['slug'];
        $model->vat = $data['vat'];
        $model->invoice_type = $data['invoice_type'];
        $model->admin_first_name = $data['admin_first_name'];
        $model->admin_last_name = $data['admin_last_name'];
        $model->admin_email = $data['admin_email'];
        $model->max_user_contracts = $data['max_user_contracts'];
        $model->max_user_amount = $data['max_user_amount'];
        $model->zip = $data['zip'];
        $model->city_id = $data['city_id'];
        $model->address = $data['address'];
        $model->phone = $data['phone'];
        $model->insurance_covered = $data['insurance_covered'];
        if ($model->insurance_covered) {
            $model->insurance_covered_amount = $data['insurance_covered_amount'];
        } else {
            $model->insurance_covered_amount = 0;
        }
        $model->insurance_covered_type = $data['insurance_covered_type'];
        $model->leasing_budget = $data['leasing_budget'];
        $model->leasing_rate = $data['leasing_rate'];
        if ($model->leasing_rate) {
            $model->leasing_rate_amount = $data['leasing_rate_amount'];
        } else {
            $model->leasing_rate_amount = 0;
        }
        $model->leasing_rate_type = $data['leasing_rate_type'];
        $model->maintenance_covered = $data['leasing_rate'];
        if ($model->maintenance_covered) {
            $model->maintenance_covered_amount = $data['leasing_rate_amount'];
        } else {
            $model->maintenance_covered_amount = 0;
        }
        $model->maintenance_covered_type = $data['maintenance_covered_type'];
        $model->portal_id = PortalHelper::id();
        $model->status_id = $data['status_id'] ?? Company::STATUS_ACTIVE;

        return $model->save() ? $model : false;
    }

    /**
     * @inheritdoc
     */
    public function list(array $params, array $relationships = []): LengthAwarePaginator
    {
        $query = $this->newQuery();

            $query
            ->where('companies.portal_id', '=', PortalHelper::id())
            ->with(['status']);

        if (!empty($params['status_id'])) {
            $query->where(['companies.status_id' => $params['status_id']]);
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
            'companies.id',
            'companies.code',
            'companies.name',
            'cities.name as city_name'
        ])
            ->join('cities', 'cities.id', '=', 'companies.city_id');

        $query = $this->applySearch($query, $params['search']);

        return $query->count();
    }

    /**
     * @param array $params
     *
     * @return Collection
     */
    public function getHistoryEmployees(array $params): Collection
    {
        $query = $this->newQuery();
        if (isset($params['portal_id'])) {
            $portalId = $params['portal_id'];
        } else {
            $portalId = PortalHelper::id();
        }

        $query->select(['companies.name as name', DB::raw('COUNT(u.id) as total')])
            ->join('portal_users as u', 'companies.id', '=', 'u.company_id')
            ->join('model_has_roles as mhr', 'mhr.model_id', '=', 'u.id')
            ->join('roles as r', 'mhr.role_id', '=', 'r.id')
            ->where('r.name', Role::ROLE_EMPLOYEE)
            ->where('companies.portal_id', $portalId)
            ->whereBetween(DB::raw('DATE(u.created_at)'), [$params['date_from'], $params['date_to']])
            ->orWhereNull('u.created_at');

        $query->groupBy('companies.id')
            ->orderBy('total', 'desc');

        return $query->withTrashed()->get();
    }

    /**
     * @param int $companyId
     * @param array $params
     * @param bool $groupByWeek
     *
     * @return Collection
     */
    public function getHistoryCompanyEmployees(int $companyId, array $params, bool $groupByWeek = false): Collection
    {
        $query = $this->newQuery();

        $dateField = $groupByWeek
            ? DB::raw('DATE(DATE_ADD(u.created_at, INTERVAL(-WEEKDAY(u.created_at)) DAY)) as week')
            : DB::raw('DATE(u.created_at) as date');

        $query->select([$dateField, DB::raw('COUNT(u.id) as total')])
            ->join('portal_users as u', 'companies.id', '=', 'u.company_id')
            ->join('model_has_roles as mhr', function ($join) {
                $join->on('mhr.model_id', '=', 'u.id')
                    ->where('mhr.model_type', '=', User::class);
            })
            ->join('roles as r', 'mhr.role_id', '=', 'r.id')
            ->where('r.name', Role::ROLE_EMPLOYEE)
            ->where('companies.id', $companyId)
            ->whereBetween(DB::raw('DATE(u.created_at)'), [$params['date_from'], $params['date_to']])
            ->orWhereNull('u.created_at');

        if ($groupByWeek) {
            $query->groupBy(DB::raw('week'));
        } else {
            $query->groupBy(DB::raw('DATE(u.created_at)'));
        }

        return $query->withTrashed()->get();
    }

    /**
     * @param int $supplierId
     * @param null|string $status
     * @param int $limit
     *
     * @return Collection
     */
    public function getSupplierOffersPerCompanyCount(int $supplierId, int $limit, ?string $status = null): Collection
    {
        $query = $this->newQuery();

        $query->select(['companies.name as name', 'companies.id as id', DB::raw('COUNT(o.id) as total')])
            ->join('portal_users as u', 'u.company_id', '=', 'companies.id')
            ->join('offers as o', 'u.id', '=', 'o.user_id')
            ->where('o.supplier_id', $supplierId);

        if ($status) {
            $query->where('o.status_id', $status);
        }

        $query->groupBy('companies.id')
            ->orderBy('total', 'desc')
            ->limit($limit);

        return $query->get();
    }

    /**
     * @param Company $company
     * @param array $supplierIds
     *
     * @return int
     */
    public function detachSuppliers(Company $company, array $supplierIds): int
    {
        $updated = 0;

        foreach ($supplierIds as $supplierId) {
            $updated += $company->suppliers()->updateExistingPivot($supplierId, ['deleted_at' => Carbon::now()]);
        }

        return $updated;
    }

    public function findBySlug(string $slug, $portalId): ?Company
    {
        return Company::query()
            ->where(['slug' => $slug, 'portal_id' => $portalId])
            ->first();
    }
}
