<?php

namespace App\Portal\Repositories\Company;

use App\Portal\Helpers\AuthHelper;
use App\Helpers\PortalHelper;
use App\Portal\Models\Contract;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ContractRepository
 *
 * @package App\Portal\Repositories\Company
 * @method Contract find(int $id, array $relations = [])
 */
class ContractRepository extends \App\Portal\Repositories\ContractRepository
{
    /** @var array */
    protected $filterWhereColumns = [
        'product' => 'product_name'
    ];
    /** @var array */
    protected $searchWhereColumns = [
        'number',
        'username',
        'product_name'
    ];

    /**
     * ContractRepository constructor.
     *
     * @param Contract $contract
     */
    public function __construct(Contract $contract)
    {
        $this->model = $contract;
    }

    /**
     * @inheritdoc
     */
    public function list(array $params, array $relationships = ['status']): LengthAwarePaginator
    {
        $companyId = AuthHelper::companyId();
        $query = $this->newQuery()
            ->where('contracts.portal_id', PortalHelper::id())
            ->where(function (Builder $q) use ($companyId) {
                $q->whereIn('contracts.company_id', function ($query) use ($companyId) {
                    $query->select('id')->from('companies')->where('parent_id', $companyId);
                })
                    ->orWhere('contracts.company_id', $companyId);
            });

        if (!empty($params['status_id']) && $params['status_id'] == Contract::STATUS_CANCELED) {
            $query->where(['contracts.status_id' => $params['status_id']]);
        } elseif (!empty($params['status_id'])) {
            $query->where('contracts.status_id', '!=', Contract::STATUS_CANCELED);
            if ($params['status_id'] == Contract::STATUS_ACTIVE) {
                $query->where('contracts.start_date', '<', Carbon::now()->toDateString());
                $query->where('contracts.end_date', '>', Carbon::now()->toDateString());
            } else {
                $query->where(function (Builder $q) {
                    $q->where('contracts.start_date', '>', Carbon::now()->toDateString());
                    $q->orWhere('contracts.end_date', '<', Carbon::now()->toDateString());
                });
            }
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
            'contracts.id'
        ])
            ->where('contracts.portal_id', PortalHelper::id())
            ->where('contracts.company_id', AuthHelper::companyId());
        $query->where('contracts.portal_id', '=', PortalHelper::id());
        $query = $this->applySearch($query, $params['search']);

        return $query->count();
    }
}
