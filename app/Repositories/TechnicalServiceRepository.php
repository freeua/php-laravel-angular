<?php

namespace App\Repositories;

use App\Modules\TechnicalServices\Models\TechnicalService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Class TechnicalServiceRepository
 *
 * @package App\System\Repositories
 * @method TechnicalService find(int $id, array $relations = [])
 */
class TechnicalServiceRepository extends BaseRepository
{

    /** @var array */
    protected $filterWhereColumns = [
        'supplier' => 's.name',
        'company'  => 'orders.company_name',
        'product'  => 'orders.product_name'
    ];
    /** @var array */
    protected $searchWhereColumns = [
        's.name',
        'p.name',
        'username',
        'product_name'
    ];

    /** @var TechnicalService */
    protected $model;

    public function __construct(TechnicalService $technicalService)
    {
        $this->model = $technicalService;
    }

    public function list(array $params, array $relationships = []): LengthAwarePaginator
    {
        $query = $this->newQuery()->with(['portal', 'supplier']);

        if (!empty($params['status_id'])) {
            $query->where(['technical_services.status_id' => $params['status_id']]);
        }

        return $this->processList($query, $params, $relationships);
    }
}
