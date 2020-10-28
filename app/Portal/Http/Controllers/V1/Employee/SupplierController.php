<?php

namespace App\Portal\Http\Controllers\V1\Employee;

use App\Portal\Helpers\AuthHelper;
use App\Portal\Http\Controllers\Controller;
use App\Portal\Http\Requests\V1\Employee\SupplierListRequest;
use App\Portal\Http\Resources\V1\CityResource;
use App\Portal\Http\Resources\V1\Employee\ListCollections\SupplierListCollection;
use App\Portal\Repositories\CityRepository;
use App\Portal\Repositories\Employee\SupplierRepository;

/**
 * Class SupplierController
 *
 * @package App\Portal\Http\Controllers\V1\Employee
 */
class SupplierController extends Controller
{
    /** @var CityRepository */
    private $cityRepository;
    /** @var SupplierRepository */
    private $supplierRepository;

    /**
     * Create a new controller instance.
     *
     * @param SupplierRepository $supplierRepository
     * @param CityRepository     $cityRepository
     */
    public function __construct(SupplierRepository $supplierRepository, CityRepository $cityRepository)
    {
        parent::__construct();

        $this->supplierRepository = $supplierRepository;
        $this->cityRepository = $cityRepository;
    }

    /**
     * Returns list of suppliers
     *
     * @param SupplierListRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(SupplierListRequest $request)
    {
        $suppliers = $this->supplierRepository->list($request->validated());

        return response()->success(new SupplierListCollection($suppliers));
    }
}
