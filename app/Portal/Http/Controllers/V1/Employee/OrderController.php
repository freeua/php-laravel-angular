<?php

namespace App\Portal\Http\Controllers\V1\Employee;

use App\Http\Requests\DefaultListRequest;
use App\Http\Resources\LeasingDocuments\OrderResource;
use App\Portal\Repositories\Employee\OrderRepository;
use App\Portal\Models\Order;
use App\Portal\Services\Employee\OrderService;

class OrderController extends \App\Portal\Http\Controllers\V1\Base\OrderController
{
    /** @var OrderRepository */
    private $orderRepository;
    /** @var OrderService */
    private $orderService;

    public function __construct(
        OrderRepository $orderRepository,
        OrderService $orderService
    ) {
        parent::__construct();

        $this->orderRepository = $orderRepository;
        $this->orderService = $orderService;
    }

    public function index(DefaultListRequest $request)
    {
        $orders = $this->orderRepository->list($request->validated());

        return response()->pagination(OrderResource::collection($orders));
    }

    public function view(Order $order)
    {
        return response()->success(new OrderResource($order->load(['status', 'user', 'supplier', 'offer', 'offer.audits'])));
    }

    public function generateOfferCertificatePdf(Order $order)
    {
        return $this->orderService->generateCertificate($order);
    }

    public function generateLeaseAgreementPdf(Order $order)
    {
        return $this->orderService->generateAgreement($order);
    }
}
