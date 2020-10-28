<?php

namespace App\Portal\Repositories;

use App\Portal\Helpers\AuthHelper;
use App\Helpers\PortalHelper;
use App\Repositories\BaseRepository;
use App\Portal\Models\Contract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ContractRepository
 *
 * @package App\Portal\Repositories\Company
 * @method Contract find(int $id, array $relations = [])
 */
class ContractRepository extends BaseRepository
{
    /** @var array */
    protected $filterWhereColumns = [
        'product'  => 'product_name'
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

    protected function applySearch(Builder $query, string $search): Builder
    {
        return $query
            ->where(function (Builder $query) use ($search) {
                return $query
                    ->orWhereHas('company', function (Builder $query) use ($search) {
                        return $query->where('name', 'like', "%$search%");
                    })
                    ->orWhereHas('product.brand', function (Builder $query) use ($search) {
                        return $query->where('name', 'like', "%$search%");
                    })
                    ->orWhereHas('product.model', function (Builder $query) use ($search) {
                        return $query->where('name', 'like', "%$search%");
                    })
                    ->orWhereHas('user', function (Builder $query) use ($search) {
                        return $query
                            ->where('first_name', 'like', "%$search%")
                            ->orWhere('last_name', 'like', "%$search%");
                    })
                    ->orWhere('number', 'like', "%$search%");
            });
    }
}
