<?php

namespace App\Portal\Http\Controllers\V1\Company;

use App\Portal\Helpers\AuthHelper;
use App\Portal\Http\Controllers\Controller;
use App\Portal\Http\Requests\V1\Company\UpdateSuppliersRequest;
use App\Portal\Http\Resources\V1\SupplierSimpleResource;
use App\Portal\Services\Company\CompanyService;

class CompanyController extends Controller
{
    /** @var CompanyService */
    private $companyService;

    public function __construct(CompanyService $companyService)
    {
        parent::__construct();

        $this->companyService = $companyService;
    }

    public function suppliers()
    {
        return response()->success(SupplierSimpleResource::collection(AuthHelper::user()->company->suppliers));
    }

    public function storeSuppliers(UpdateSuppliersRequest $request)
    {
        $suppliers = $this->companyService->storeSuppliers($request->get('ids'));

        return response()->success(SupplierSimpleResource::collection($suppliers));
    }
}
