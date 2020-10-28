<?php

namespace App\Portal\Repositories\Company;

use App\Helpers\PortalHelper;
use App\Modules\TechnicalServices\Models\TechnicalService;
use App\Portal\Helpers\AuthHelper;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Class TechnicalServiceRepository
 *
 * @package App\Portal\Repositories\Company
 * @method TechnicalService find(int $id, array $relations = [])
 */
class TechnicalServicesRepository extends \App\Modules\TechnicalServices\Repositories\TechnicalServicesRepository
{
    /** @var array */
    protected $filterWhereColumns = [
        'supplier' => 's.name',
        'product'  => 'pm.name'
    ];
    /** @var array */
    protected $searchWhereColumns = [
        'number',
        's.name',
        'pm.name',
        'CONCAT(u.first_name, " ", u.last_name)'
    ];

    /**
     * @inheritdoc
     */
    public function list(array $params, array $relationships = []): LengthAwarePaginator
    {
        $companyId = AuthHelper::companyId();
        $query = $this->newQuery()
            ->where('technical_services.portal_id', PortalHelper::id())
            ->whereIn('technical_services.company_id', function ($query) use ($companyId) {
                $query->select('id')->from('companies')->where('parent_id', $companyId);
            })
            ->orWhere('technical_services.company_id', $companyId)
            ->with(['supplier', 'user', 'product']);
        if (!empty($params['status_id'])) {
            $query->where(['technical_services.status_id' => $params['status_id']]);
        }

        return $this->processList($query, $params, $relationships);
    }
    public function listForEmployeeOfCompany($employee_id, array $params, array $relationships = []): LengthAwarePaginator
    {
        $query = $this->newQuery()
            ->where('technical_services.portal_id', PortalHelper::id())
            ->where('technical_services.company_id', AuthHelper::companyId())
            ->where('technical_services.user_id', $employee_id)
            ->with(['supplier', 'user', 'product'])
        ;
        if (!empty($params['status_id'])) {
            $query->where(['technical_services.status_id' => $params['status_id']]);
        }

        return $this->processList($query, $params, $relationships);
    }
}
