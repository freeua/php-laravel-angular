<?php

namespace App\Portal\Repositories;

use App\Repositories\BaseRepository;
use App\Models\City;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\JoinClause;

/**
 * Class CityRepository
 *
 * @package App\Portal\Repositories
 * @method City find(int $id, array $relations = [])
 */
class CityRepository extends BaseRepository
{
    /**
     * CityRepository constructor.
     *
     * @param City $city
     */
    public function __construct(City $city)
    {
        $this->model = $city;
    }

    /**
     * @param int $companyId
     *
     * @return Collection
     * @throws \Exception
     */
    public function getCompanySuppliersCities(int $companyId): Collection
    {
        return $this->newQuery()
            ->select(['cities.*'])
            ->join('suppliers as s', 'cities.id', '=', 's.city_id')
            ->join('company_supplier as cs', function (JoinClause $join) use ($companyId) {
                $join->on('cs.supplier_id', '=', 's.id')->where('cs.company_id', $companyId)->whereNull('cs.deleted_at');
            })
            ->groupBy('cities.id')
            ->orderBy('name')
            ->get();
    }
}
