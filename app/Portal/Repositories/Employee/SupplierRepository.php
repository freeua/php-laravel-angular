<?php

namespace App\Portal\Repositories\Employee;

use App\Portal\Helpers\AuthHelper;
use App\Helpers\PortalHelper;
use App\Repositories\BaseRepository;
use App\Portal\Models\Supplier;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * Class SupplierRepository
 *
 * @package App\Portal\Repositories\Employee
 *
 * @method Supplier find(int $id, array $relations = [])
 */
class SupplierRepository extends BaseRepository
{
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
     * @inheritdoc
     */
    public function list(array $params, array $relationships = []): LengthAwarePaginator
    {
        $query = $this->newQuery();

        $query->select([
            'suppliers.id',
            'suppliers.name',
            'c.name as city_name',
            'suppliers.zip',
            'suppliers.address',
            'suppliers.admin_email',
            'suppliers.phone',
            'suppliers.logo as logo',
        ])
            ->join('cities as c', 'c.id', '=', 'suppliers.city_id')
            ->join('portal_supplier', 'portal_supplier.supplier_id', '=', 'suppliers.id')
            ->join('company_supplier as cs', function ($join) {
                $join->on('cs.supplier_id', '=', 'suppliers.id');
            })
            ->leftJoin('products as p', function ($join) {
                $join->on('p.supplier_id', '=', 'suppliers.id')->whereNull('p.deleted_at');
            })
            ->groupBy('suppliers.id');
        $query->where('cs.company_id', AuthHelper::companyId())->whereNull('cs.deleted_at');
        $query->where('portal_supplier.portal_id', '=', PortalHelper::id());
        if (!empty($params['cities'])) {
            $query->whereIn('c.id', $params['cities']);
        }

        if (!empty($params['categories'])) {
            $query->whereIn('p.category_id', $params['categories']);
        }

        if (empty($params['order'])) {
            $params['order_by'] = 'city_name';
            $params['order'] = 'asc';
        }

        return $this->processList($query, $params, $relationships);
    }
}
