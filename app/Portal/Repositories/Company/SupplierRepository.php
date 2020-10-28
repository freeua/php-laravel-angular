<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 12.03.2019
 * Time: 11:30
 */

namespace App\Portal\Repositories\Company;

use App\Helpers\PortalHelper;
use App\Portal\Helpers\AuthHelper;
use App\Portal\Models\Supplier;
use App\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

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


    public function searchTotal(array $params): int
    {
        $query = $this->newQuery()->select(['id']);
        $this->applySearch($query, $params['search']);
        return $query
            ->count();
    }

    protected function applySearch(Builder $query, string $search): Builder
    {
        return $query
            ->where(function (Builder $query) use ($search) {
                return $query
                    ->orWhere('code', 'like', "%$search%")
                    ->orWhere('name', 'like', "%$search%");
            });
    }
}
