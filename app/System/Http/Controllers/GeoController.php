<?php

namespace App\System\Http\Controllers;

use App\System\Http\Resources\CityResource;
use App\Repositories\CityRepository;

/**
 * Class GeoController
 *
 * @package App\System\Http\Controllers
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
