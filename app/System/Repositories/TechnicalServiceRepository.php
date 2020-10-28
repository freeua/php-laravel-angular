<?php

namespace App\System\Repositories;

use App\Modules\TechnicalServices\Models\TechnicalService;
use App\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Class TechnicalServiceRepository
 *
 * @package App\System\Repositories
 * @method TechnicalService find(int $id, array $relations = [])
 */
class TechnicalServiceRepository extends BaseRepository
{

    /** @var TechnicalService */
    protected $model;

    /**
     * TechnicalService constructor.
     *
     * @param TechnicalService $technicalService
     */
    public function __construct(TechnicalService $technicalService)
    {
        $this->model = $technicalService;
    }


    public function listForEmployee($employee_id, array $params, array $relationships = []): LengthAwarePaginator
    {
        $query = $this->newQuery()
            ->where('technical_services.user_id', $employee_id)
            ->with(['supplier', 'user', 'product'])
        ;
        if (!empty($params['status_id'])) {
            $query->where(['technical_services.status_id' => $params['status_id']]);
        }

        return $this->processList($query, $params, $relationships);
    }
}
