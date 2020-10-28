<?php

namespace App\Console\Commands;

use App\System\Services\ContractService;
use Carbon\Carbon;
use Illuminate\Console\Command;

/**
 * Class ActivateContracts
 *
 * @package App\Console\Commands
 */
class ActivateContracts extends Command
{
    /** @var ContractService */
    private $contractService;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contract:activate';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set status to "Aktiv" for contracts which start in current month';

    /**
     * Create a new command instance.
     *
     * @param ContractService $contractService
     */
    public function __construct(ContractService $contractService)
    {
        parent::__construct();

        $this->contractService = $contractService;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $activated = $this->contractService->activate();

        $this->line(sprintf('[%s] Total contracts activated: %s', Carbon::now(), $activated));
    }
}
