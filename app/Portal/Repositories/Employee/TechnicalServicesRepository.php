<?php

namespace App\Portal\Repositories\Employee;

use App\Helpers\PortalHelper;
use App\Models\Rates\ServiceRate;
use App\Modules\TechnicalServices\Models\TechnicalService;
use App\Portal\Helpers\AuthHelper;
use App\Portal\Models\Contract;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Class TechnicalServiceRepository
 *
 * @package App\Portal\Repositories\Employee
 * @method TechnicalService find(int $id, array $relations = [])
 */
class TechnicalServicesRepository extends \App\Modules\TechnicalServices\Repositories\TechnicalServicesRepository
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
        $query = $this->newQuery()
            ->where('technical_services.portal_id', PortalHelper::id())
            ->where('technical_services.user_id', AuthHelper::id())
            ->with(['supplier']);
        if (!empty($params['status_id'])) {
            $query->where(['technical_services.status_id' => $params['status_id']]);
        }

        return $this->processList($query, $params, $relationships);
    }

    public function getFullServiceContracts(): Collection
    {
        $query = Contract::query()
            ->where('portal_id', PortalHelper::id())
            ->where('user_id', AuthHelper::id())
            ->where('end_date', '>', Carbon::now())
            ->where('service_rate_modality', ServiceRate::FULL_SERVICE)
            ->with(['technicalServices']);

        return $query->get();
    }
}
