<?php

namespace App\System\Services;

use App\Portal\Models\Contract;
use App\Portal\Models\Order;
use App\System\Repositories\ContractRepository;
use App\Repositories\OrderRepository;
use Carbon\Carbon;

/**
 * Class OrderService
 *
 * @package App\System\Services
 */
class OrderService
{
    /** @var OrderRepository */
    private $orderRepository;
    /** @var ContractRepository */
    private $contractRepository;

    /**
     * OrderService constructor.
     *
     * @param OrderRepository         $orderRepository
     * @param ContractRepository      $contractRepository
     */
    public function __construct(
        OrderRepository $orderRepository,
        ContractRepository $contractRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->contractRepository = $contractRepository;
    }

    /**
     * @param int $id
     *
     * @return Order
     */
    public function getOrder(int $id)
    {
        /* @var Order $order */
        $order = $this->orderRepository->find($id);

        return $order;
    }

    /**
     * @param Order $order
     * @param array $data
     *
     * @return Order
     */
    public function update(Order $order, array $data): Order
    {
        $order->agreedPurchasePrice = $data['agreed_purchase_price'];
        $order->leasingRate = $data['leasing_rate'];
        $order->insurance = $data['insurance'];
        $order->calculatedResidualValue = $data['calculated_residual_value'];
        $order->leasingPeriod = $data['leasing_period'];
        $order->status_id = $data['status_id'];
        $order->productSize = $data['product_size'] ?? null;
        $order->save();

        return $order;
    }

    /**
     * @param Order $order
     *
     * @return Contract|false
     */
    public function convert(Order $order)
    {

        $startDate = (new Carbon())->startOfMonth();
        $endDate = $startDate->copy()->addMonths($order->leasingPeriod)->subDays(1);

        //Todo calculate leasing_rate in product service as Purchase price * Leasing Factor
        $contract = $this->contractRepository->create([
            'order_id'                  => $order->id,
            'portal_id'                 => $order->portal_id,
            'supplier_id'               => $order->supplier_id,
            'user_id'                   => $order->user_id,
            'product_id'                => $order->product_id,
            'company_id'                => $order->company_id,
            'username'                  => $order->employeeName,
            'company_name'              => $order->companyName,
            'product_supplier'          => $order->product->supplier->name,
            'product_brand'             => $order->product->brand->name,
            'product_category'          => $order->product->category->name,
            'productModel'              => $order->productModel,
            'status'                    => Contract::STATUS_INACTIVE,
            'agreed_purchase_price'     => $order->agreedPurchasePrice,
            'leasing_rate_type'         => $order->leasing_rate_type,
            'leasing_rate'              => $order->leasingRate,
            'insurance_type'            => $order->insurance_type,
            'insurance'                 => $order->insurance,
            'calculated_residual_value' => $order->calculatedResidualValue,
            'leasing_period'            => $order->leasingPeriod,
            'product_size'              => $order->productSize,
            'product_notes'             => $order->offer->productNotes,
            'start_date'                => $startDate->toDateString(),
            'end_date'                  => $endDate->toDateString(),
        ]);

        if ($contract) {
            $order->status = Order::STATUS_SUCCESSFUL;
            $order->save();
        }

        return $contract;
    }
}
