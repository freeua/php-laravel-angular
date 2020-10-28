<?php

namespace App\System\Services;

use App\Portal\Notifications\Contract\ContractStarted;
use App\Portal\Repositories\UserRepository;
use App\Portal\Models\Role;
use App\Repositories\BaseRepository;
use App\Portal\Models\Contract;
use App\System\Repositories\ContractRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Services\Emails\EmailService;

/**
 * Class ContractService
 *
 * @package App\System\Services
 */
class ContractService extends BaseRepository
{
    /** @var UserRepository */
    private $userRepository;
    /** @var ContractRepository */
    private $contractRepository;

    /**
     * ContractService constructor.
     *
     * @param ContractRepository $contractRepository
     * @param UserRepository $userRepository
     */
    public function __construct(
        ContractRepository $contractRepository,
        UserRepository $userRepository
    ) {
        $this->contractRepository = $contractRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @param null|string $date
     *
     * @return int
     */
    public function activate(?string $date = null)
    {
        $date = $date ?? Carbon::now()->startOfMonth()->toDateString();

        $contracts = $this->contractRepository->newQuery()
            ->whereNotNull('pickup_code')
            ->where('start_date', $date)
            ->where('status', Contract::STATUS_INACTIVE)
            ->orderBy('portal_id')
            ->get();

        $total = 0;

        foreach ($contracts as $contract) {
            if ($this->contractRepository->update($contract->id, ['status' => Contract::STATUS_ACTIVE])) {
                $portalAdmins = $this->userRepository->findByRole(Role::ROLE_PORTAL_ADMIN, $contract->portal_id);
                if ($portalAdmins) {
                    Notification::send($portalAdmins, (new ContractStarted($contract, $contract->portal->domain)));
                }
                $supplierAdmins = $this->userRepository->findByRole(Role::ROLE_SUPPLIER_ADMIN, $contract->portal_id, $contract->supplier->portalSupplier->id);
                if ($supplierAdmins) {
                    Notification::send($supplierAdmins, (new ContractStarted($contract, $contract->portal->domain)));
                }
                $companyAdmins = $this->userRepository->findByRole(Role::ROLE_COMPANY_ADMIN, $contract->portal_id, null, $contract->company_id);
                if ($companyAdmins) {
                    Notification::send($companyAdmins, (new ContractStarted($contract, $contract->portal->domain)));
                }

                ++$total;
            }
        }

        $contracts = $this->contractRepository->newQuery()
            ->select([DB::raw('COUNT(*) as active_count'), 'user_id', 'portal_id'])
            ->where('status', Contract::STATUS_ACTIVE)
            ->groupBy('portal_id', 'user_id')
            ->orderBy('portal_id')
            ->get();

        foreach ($contracts as $contract) {
            $this->userRepository->update($contract->user_id, ['active_contracts' => $contract->active_count]);
        }

        return $total;
    }
}
