<?php

namespace App\Portal\Http\Controllers\V1;

use App\Portal\Http\Controllers\Controller;
use App\Portal\Http\Resources\V1\CityResource;
use App\Portal\Repositories\CityRepository;

/**
 * Class GeoController
 *
 * @package App\Portal\Http\Controllers\V1
 */
class GeoController extends Controller
{
    public $cityRepository;

    /**
     * Create a new controller instance.
     *
     * @param CityRepository $cityRepository
     */
    public function __construct(CityRepository $cityRepository)
    {
        parent::__construct();

        $this->cityRepository = $cityRepository;
    }

    /**
     * Get list of cities
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function cities()
    {
        return response()->success(
            CityResource::collection($this->cityRepository->all('name'))
        );
    }
}
