<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 11.03.2019
 * Time: 17:03
 */

namespace App\Portal\Http\Controllers\V1\Company;

use App\Portal\Http\Controllers\Controller;
use App\Http\Requests\DefaultListRequest;
use App\Portal\Http\Resources\V1\Company\CompanySupplierResource;
use App\Portal\Http\Resources\V1\SupplierResource;
use App\Portal\Models\Supplier;
use App\Portal\Repositories\Company\SupplierRepository;

class SupplierController extends Controller
{
    /** @var SupplierRepository */
    private $supplierRepository;

    /**
     * Create a new controller instance.
     *
     * @param SupplierRepository $supplierRepository
     */
    public function __construct(SupplierRepository $supplierRepository)
    {
        parent::__construct();

        $this->supplierRepository = $supplierRepository;
    }

    /**
     * @param DefaultListRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(DefaultListRequest $request)
    {
        $suppliers = $this->supplierRepository->list($request->validated());

        return response()->pagination(SupplierResource::collection($suppliers));
    }

    /**
     * @param Supplier $supplier
     * @return \Illuminate\Http\JsonResponse
     */
    public function view(Supplier $supplier)
    {

        return response()->success(new CompanySupplierResource($supplier));
    }
}
