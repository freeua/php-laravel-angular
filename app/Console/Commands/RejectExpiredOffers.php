<?php

namespace App\Console\Commands;

use App\Portal\Services\OfferService;
use App\System\Repositories\PortalRepository;
use Carbon\Carbon;
use Illuminate\Console\Command;

/**
 * Class RejectExpiredOffers
 *
 * @package App\Console\Commands
 */
class RejectExpiredOffers extends Command
{
    /** @var OfferService */
    private $offerService;
    /** @var PortalRepository */
    private $portalRepository;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'offer:reject-expired';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set status to "Abgelehnt" for expired offers';

    /**
     * Create a new command instance.
     *
     * @param OfferService $offerService
     * @param PortalRepository $portalRepository
     */
    public function __construct(OfferService $offerService, PortalRepository $portalRepository)
    {
        parent::__construct();
        $this->offerService = $offerService;
        $this->portalRepository = $portalRepository;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $totalRejected = 0;

        $rejected = $this->offerService->rejectExpired();
        if ($rejected) {
            $totalRejected += $rejected;
        }

        $this->line(sprintf('[%s] Total offers rejected: %s', Carbon::now(), $totalRejected));
    }
}
