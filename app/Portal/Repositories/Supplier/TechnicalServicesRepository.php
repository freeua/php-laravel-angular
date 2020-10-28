<?php

namespace App\Portal\Repositories\Supplier;

use App\Models\Rates\ServiceRate;
use App\Modules\TechnicalServices\Models\TechnicalService;
use App\Portal\Helpers\AuthHelper;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Class TechnicalServiceRepository
 *
 * @package App\Portal\Repositories\Supplier
 * @method TechnicalService find(int $id, array $relations = [])
 */
class TechnicalServicesRepository extends \App\Modules\TechnicalServices\Repositories\TechnicalServicesRepository
{

    /** @var array */
    protected $filterWhereColumns = [
        'company' => 'c.name',
        'product' => 'pm.name',
        'date'    => ['IF(technical_services.status_updated_at, technical_services.status_updated_at, technical_services.created_at)', 'between', 'timestamps', '|'],
    ];
    /** @var array */
    protected $searchWhereColumns = [
        'number',
        'c.name',
        'pm.name',
        'CONCAT(u.first_name, " ", u.last_name)'
    ];

    /**
     * @inheritdoc
     */
    public function list(array $params, array $relationships = []): LengthAwarePaginator
    {

        $query = $this->newQuery();

        $query->where('technical_services.supplier_id', AuthHelper::supplierId());

        if (!empty($params['status_id'])) {
            $query->where(['technical_services.status_id' => $params['status_id']]);
        }

        return $this->processList($query, $params, $relationships);
    }
    public function listInspections(array $params, array $relationships = []): LengthAwarePaginator
    {
        $query = $this->newQuery();

        $query->where('technical_services.supplier_id', AuthHelper::supplierId());
        $query->where('technical_services.service_modality', '=', ServiceRate::INSPECTION);

        if (!empty($params['status_id'])) {
            $query->where(['technical_services.status_id' => $params['status_id']]);
        }

        return $this->processList($query, $params, $relationships);
    }
    public function listServices(array $params, array $relationships = []): LengthAwarePaginator
    {

        $query = $this->newQuery();

        $query->where('technical_services.supplier_id', AuthHelper::supplierId());
        $query->where('technical_services.service_modality', '!=', ServiceRate::INSPECTION);

        if (!empty($params['status_id'])) {
            $query->where(['technical_services.status_id' => $params['status_id']]);
        }

        return $this->processList($query, $params, $relationships);
    }
}
