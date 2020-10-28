<?php

namespace App\Portal\Http\Controllers\V1\Employee;

use App\Http\Requests\DefaultListRequest;
use App\Portal\Http\Controllers\Controller;
use App\Http\Resources\LeasingDocuments\ContractResource;
use App\Portal\Repositories\Employee\ContractRepository;
use App\Portal\Models\Contract;

/**
 * Class ContractController
 *
 * @package App\Portal\Http\Controllers\V1\Employee
 */
class ContractController extends Controller
{
    /** @var ContractRepository */
    private $contractRepository;

    /**
     * ContractController constructor.
     *
     * @param ContractRepository $contractRepository
     */
    public function __construct(ContractRepository $contractRepository)
    {
        parent::__construct();

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
        $contracts = $this->contractRepository->list($request->validated(), ['status']);
        return response()->pagination(ContractResource::collection($contracts));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function statuses()
    {
        return response()->success(Contract::getStatuses());
    }

    /**
     * View a contract
     *
     * @param Contract $contract
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function view(Contract $contract)
    {
        return response()->success(new ContractResource($contract->load(['status', 'order.offer', 'order.offer.audits'])));
    }
}
