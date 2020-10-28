<?php

namespace App\Portal\Http\Controllers\V1\Company;

use App\Http\Requests\DefaultListRequest;
use App\Portal\Http\Controllers\Controller;
use App\Http\Resources\LeasingDocuments\ContractResource;
use App\Portal\Repositories\Company\ContractRepository;
use App\Portal\Models\Contract;
use App\Portal\Services\Company\ContractService;
use Illuminate\Http\Request;

/**
 * Class ContractController
 *
 * @package App\Portal\Http\Controllers\V1\Company
 */
class ContractController extends Controller
{
    /** @var ContractRepository */
    private $contractRepository;
    /** @var ContractService */
    private $contractService;

    /**
     * ContractController constructor.
     *
     * @param ContractRepository $contractRepository
     * @param ContractService $contractService
     */
    public function __construct(
        ContractRepository $contractRepository,
        ContractService $contractService
    ) {
        parent::__construct();

        $this->contractRepository = $contractRepository;
        $this->contractService = $contractService;
    }

    /**
     * Returns list of contracts
     *
     * @param DefaultListRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(DefaultListRequest $request)
    {
        $contracts = $this->contractRepository->list($request->validated(), ['user', 'status', 'supplier']);

        return response()->pagination(ContractResource::collection($contracts));
    }

    /**
     * View an contract
     *
     * @param Contract $contract
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function view(Contract $contract)
    {
        return response()->success(new ContractResource($contract->load(['order.offer.audits', 'user', 'status', 'order.offer', 'supplier'])));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function statuses()
    {
        return response()->success(Contract::getStatuses());
    }

    /**
     * @param Request $request
     * @return \Illuminate\Auth\Access\Response
     */
    public function export(Request $request)
    {
        $target = $request->input('exportSettings.target');
        $format = $request->input('exportSettings.format');

        return $format === 'pdf'
            ? $this->contractService->generatePDFExport($target)
            : $this->contractService->generateExcelExport($target);
    }
}
