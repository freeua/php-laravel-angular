<?php
namespace App\Leasings\Services;

use App\Portal\Models\Contract;
use App\Portal\Models\Order;
use Carbon\Carbon;

class ContractService
{

    public static function createFromOrder(Order $order)
    {
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
        $contract->saveOrFail();
        return $contract;
    }
}
