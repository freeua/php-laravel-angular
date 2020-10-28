<?php

namespace App\Modules\TechnicalServices\Controllers;

use App\Http\Resources\LeasingDocuments\ContractResource;
use App\Modules\TechnicalServices\Models\TechnicalService;
use App\Modules\TechnicalServices\Repositories\TechnicalServicesRepository;
use App\Modules\TechnicalServices\Requests\CreateTechnicalServiceRequest;
use App\Modules\TechnicalServices\Requests\TechnicalServiceAcceptRequest;
use App\Modules\TechnicalServices\Requests\TechnicalServiceCompletedRequest;
use App\Modules\TechnicalServices\Requests\TechnicalServicesListRequest;
use App\Modules\TechnicalServices\Resources\TechnicalServiceListCollection;
use App\Modules\TechnicalServices\Resources\TechnicalServiceResource;
use App\Modules\TechnicalServices\Services\TechnicalServicesService;
use App\Modules\TechnicalServices\Transformers\TechnicalServiceListTransformer;
use App\Portal\Helpers\AuthHelper;
use App\Portal\Http\Controllers\Controller;
use App\Portal\Models\Contract;

class TechnicalServiceController extends Controller
{

    public function index(TechnicalServicesListRequest $request)
    {
        $technicalServices = TechnicalServicesRepository::list(new TechnicalServiceListTransformer($request));

        return response()->jsonPagination(TechnicalServiceListCollection::collection($technicalServices));
    }

    public function view(TechnicalService $technicalService)
    {
        if (!AuthHelper::isSupplier()) {
            $technicalService->load('audits');
        }
        return response()->json(new TechnicalServiceResource(
            $technicalService->load('contract')
        ));
    }

    public function fullServiceContracts()
    {
        $contracts = TechnicalServicesRepository::getFullServiceContracts();

        return response()->json(ContractResource::collection($contracts));
    }

    public function createFromContract(CreateTechnicalServiceRequest $request, Contract $contract)
    {
        $technicalService = TechnicalServicesService::createFromContract($contract);

        return response()->json(new TechnicalServiceResource(
            $technicalService->load('contract', 'audits')
        ));
    }

    public function accept(TechnicalService $technicalService, TechnicalServiceAcceptRequest $request)
    {
        TechnicalServicesService::accept($technicalService, $request->validated());

        return response()->json(new TechnicalServiceResource(
            $technicalService->load('contract')
        ));
    }

    public function ready(TechnicalService $technicalService)
    {
        TechnicalServicesService::ready($technicalService);

        return response()->json(new TechnicalServiceResource(
            $technicalService->load('contract')
        ));
    }

    public function complete(TechnicalService $technicalService, TechnicalServiceCompletedRequest $request)
    {
        TechnicalServicesService::complete($technicalService, $request->validated());

        return response()->json(new TechnicalServiceResource(
            $technicalService->load('contract')
        ));
    }

    public function generateServicePdf()
    {
        return TechnicalServicesService::servicePDF();
    }
}
