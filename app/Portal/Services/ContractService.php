<?php

namespace App\Portal\Services;

use App\Exceptions\ContractNotFoundException;
use App\Helpers\PortalHelper;
use App\Modules\TechnicalServices\Models\TechnicalService;
use App\Portal\Models\Contract;
use App\Portal\Models\Order;
use App\Portal\Repositories\Supplier\UserRepository;
use App\System\Repositories\ContractRepository;
use Carbon\Carbon;

class ContractService
{
    /** @var UserRepository */
    private $userRepository;
    /** @var ContractRepository */
    private $contractRepository;

    public function __construct(ContractRepository $contractRepository, UserRepository $userRepository)
    {
        $this->contractRepository = $contractRepository;
        $this->userRepository = $userRepository;
    }

    public function createFromOrder(Order $order)
    {
        $orderParams = $order->toArray();
        $contract = new Contract($order->toArray());
        $contract->order_id = $order->id;
        $contract->portal_id = $order->portal_id;
        $contract->user_id = $order->user_id;
        $contract->pickup_code = $order->pickup_code;
        $contract->supplier_id = $order->supplier_id;
        $contract->supplierCode = $order->supplierCode;
        $contract->employeeCode = $order->employeeCode;
        $contract->product_category_id = $order->product_category_id;
        $contract->company_id = $order->company_id;
        $contract->startDate = Carbon::now()->day === 1 ? Carbon::now()->startOfMonth() : Carbon::now()->startOfMonth()->addMonth();
        $contract->endDate = $contract->startDate->copy()->addMonths($order->leasingPeriod)->subDays(1);
        $contract->statusId = Contract::STATUS_INACTIVE;
        $contract->serialNumber = $order->frame_number;
        $contract->serviceRateModality = $order->serviceRateModality;
        $contract->serviceBudget = $order->offer->serviceRate->budget;
        $contract->saveOrFail();
        return $contract;
    }
    public function search($request)
    {
        $contract = Contract::query()
             ->where($request)
             ->where('portal_id',PortalHelper::id())
            ->first();
        if (!$contract) {
            throw new ContractNotFoundException();
        }
        return $contract;
    }
    public function cancel(Contract $contract, array $data)
    {
        $contract->fill($data);
        $contract->statusId = Contract::STATUS_CANCELED;
        $contract->order->status_id = Order::STATUS_CANCELED_CONTRACT;
        $contract->order->save();
        $contract->technicalServices()
            ->where('status_id', '!=', TechnicalService::STATUS_SUCCESSFUL)
        ->update(['status_id' => TechnicalService::STATUS_CONTRACT_CANCELLED]);

        $updated = $contract->saveOrFail();

        return $contract;
    }
}
