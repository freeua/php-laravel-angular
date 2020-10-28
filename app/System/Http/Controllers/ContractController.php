<?php

namespace App\System\Http\Controllers;

use App\Http\Resources\LeasingDocuments\ContractResource;
use App\System\Exports\ContractsExport;
use App\System\Exports\ContractExport;
use App\Http\Requests\DefaultListRequest;
use App\System\Http\Resources\ListCollections\ContractListCollection;
use App\System\Repositories\ContractRepository;
use Maatwebsite\Excel\Excel;
use App\Portal\Models\Contract;

/**
 * Class ContractController
 *
 * @package App\System\Http\Controllers
 */
class ContractController extends Controller
{
    /** @var ContractRepository */
    private $contractRepository;

    /**
     * Create a new controller instance.
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
        $contracts = $this->contractRepository->list($request->validated());

        return response()->pagination(ContractResource::collection($contracts));
    }

    /**
     * Export list of contracts
     *
     * @param Excel           $excel
     * @param ContractsExport $export
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function export(Excel $excel, ContractsExport $export)
    {
        return $excel->download($export, 'Contracts.xlsx', Excel::XLSX);
    }

    /**
     * @param Contract $contract
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function view(Contract $contract)
    {
        $order = $this->contractRepository->getContract($contract->id);

        return response()->success(new ContractResource($order->load(['supplier', 'user', 'order.offer.audits'])));
    }

    /**
     * Export single contract
     *
     * @param Contract $contract
     * @param Excel    $excel
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function exportSingle(Contract $contract, Excel $excel)
    {
        $contract = $this->contractRepository->getContract($contract->id);
        $export = new ContractExport($contract);

        return $excel->download($export, 'Contract #' . $contract->number . '.xlsx', Excel::XLSX);
    }
}
