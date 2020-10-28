<?php

namespace App\System\Repositories;

use App\Repositories\BaseRepository;
use App\Portal\Models\Supplier;
use App\Models\Portal;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Class SupplierRepository
 *
 * @package App\System\Repositories
 * @method Supplier find(int $id, array $relations = [])
 */
class SupplierRepository extends BaseRepository
{
    /** @var array */
    protected $filterWhereColumns = [
        'city' => 'c.name',
        'name' => 'suppliers.name',
    ];
    /** @var array */
    protected $searchWhereColumns = [
        'suppliers.name',
        'suppliers.admin_email',
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
     * @inheritdoc
     */
    public function list(array $params, array $relationships = []): LengthAwarePaginator
    {
        $query = $this->newQuery();

        $query->select([
            'suppliers.*',
            'c.name as city_name'
        ])
            ->join('cities as c', 'c.id', '=', 'suppliers.city_id');

        if (!empty($params['status_id'])) {
            $query->where(['suppliers.status_id' => $params['status_id']]);
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
            'suppliers.admin_email',
        ])
            ->join('cities as c', 'c.id', '=', 'suppliers.city_id');

        $query = $this->applySearch($query, $params['search']);

        return $query->count();
    }

    /**
     * @param array $data
     *
     * @return Supplier|false
     */
    public function create(array $data)
    {
        $supplier = $this->model->newInstance();

        $supplier->name = $data['name'];
        $supplier->admin_first_name = $data['admin_first_name'];
        $supplier->admin_last_name = $data['admin_last_name'];
        $supplier->admin_email = $data['admin_email'];
        $supplier->city_id = $data['city_id'];
        $supplier->vat = $data['vat'];
        $supplier->phone = $data['phone'];
        $supplier->address = $data['address'];
        $supplier->status_id = $data['status_id'];
        $supplier->gp_number = $data['gp_number'];
        $supplier->bank_account = $data['bank_account'];
        $supplier->bank_name = $data['bank_name'];
        $supplier->grefo = $data['grefo'];
        $portal = app(Portal::class)->newQuery()->find($data['portal_id']);
        if ($supplier->save()) {
            $supplier->portals()->save($portal, ['status_id' => Supplier::STATUS_ACTIVE]);
            return $supplier;
        }

        return false;
    }

    /**
     * @param Supplier $supplier
     *
     * @return Collection
     */
    public function getInstalledOnPortals(Supplier $supplier): Collection
    {

        return $this
            ->newQuery()
            ->select('portal_id')
            ->with('portal')
            ->where('id', $supplier->portal_id)
            ->get()
            ->pluck('portal')
            ->unique();
    }
}
