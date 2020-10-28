<?php

namespace App\Repositories;

use App\Models\City;

/**
 * Class CityRepository
 *
 * @package App\System\Repositories
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
}
