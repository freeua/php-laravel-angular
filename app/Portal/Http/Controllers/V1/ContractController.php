<?php

namespace App\Portal\Http\Controllers\V1;

use App\Exceptions\ContractNotFoundException;
use App\Http\Requests\DefaultListRequest;
use App\Portal\Http\Controllers\Controller;
use App\Http\Resources\LeasingDocuments\ContractResource;
use App\Portal\Http\Requests\V1\CancelContractRequest;
use App\Portal\Http\Requests\V1\SearchContractRequest;
use App\System\Repositories\ContractRepository;
use App\Portal\Models\Contract;
use App\Portal\Services\ContractService;

/**
 * Class ContractController
 *
 * @package App\Portal\Http\Controllers\V1\Employee
 */
class ContractController extends Controller
{
    private $contractService;
    private $contractRepository;

    /**
     * ContractController constructor.
     *
     * @param ContractRepository $contractRepository
     */
    public function __construct(ContractService $contractService, ContractRepository $contractRepository)
    {
        parent::__construct();
        $this->contractService = $contractService;
        $this->contractRepository = $contractRepository;
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
        $contracts = $this->contractRepository->listForPortal($request->validated(), ['user', 'status', 'supplier']);

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
        return response()->json(new ContractResource($contract->load(['order.offer.audits', 'user', 'status', 'order.offer', 'supplier'])));
    }

    public function search(SearchContractRequest $request)
    {
        $contract = $this->contractService->search($request->validated());
        $order = $this->contractRepository->getContract($contract->id);
        return response()->json(new ContractResource($order->load(['supplier', 'user', 'order.offer.audits'])));
    }

    public function cancel(Contract $contract, CancelContractRequest $request)
    {
        $contract = $this->contractService->cancel($contract, $request->validated());
        return response()->json(new ContractResource($contract->load(['status', 'order.offer', 'order.offer.audits'])));
    }
}
