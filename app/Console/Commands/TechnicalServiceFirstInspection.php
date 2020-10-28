<?php

namespace App\Console\Commands;

use App\Modules\TechnicalServices\Repositories\ContractsRepository;
use App\Modules\TechnicalServices\Services\TechnicalServicesService;
use App\Repositories\OrderRepository;
use Illuminate\Console\Command;

class TechnicalServiceFirstInspection extends Command
{
    /** @var TechnicalServicesService */
    private $technicalServiceService;
    /** @var OrderRepository */
    private $orderRepository;
    protected $signature = 'technicalService:first-inspection';
    protected $description = 'Send email of first inspection after a half year of pickup';

    public function __construct(
        TechnicalServicesService $technicalServiceService,
        OrderRepository $orderRepository
    ) {
        parent::__construct();
        $this->technicalServiceService = $technicalServiceService;
        $this->orderRepository = $orderRepository;
    }

    public function handle()
    {
        $resultContractNumbers = [];
        $contracts = ContractsRepository::contractsForFirstInspectionHalfYear();
        foreach ($contracts as $contract) {
            $technicalService = TechnicalServicesService::createFromContract($contract);

            $resultContractNumbers[] = $technicalService->contract->number;
        }
        if (!empty($resultContractNumbers)) {
            $this->line(sprintf('List of contracts that have been successfully generated an inspection: %s', implode(',', $resultContractNumbers)));
        } else {
            $this->line('No contracts to generate inspections');
        }
    }
}
