<?php

namespace App\Portal\Http\Controllers\V1\Company;

use App\Http\Requests\DefaultListRequest;
use App\Http\Resources\LeasingDocuments\OrderResource;
use App\Portal\Repositories\Company\OrderRepository;
use App\Portal\Models\Order;
use App\Portal\Services\Company\OrderService;
use Illuminate\Http\Request;

/**
 * Class OrderController
 *
 * @package App\Portal\Http\Controllers\V1\Company
 */
class OrderController extends \App\Portal\Http\Controllers\V1\Base\OrderController
{
    /** @var OrderService */
    private $orderService;
    /** @var OrderRepository */
    private $orderRepository;

    /**
     * OrderController constructor.
     *
     * @param OrderRepository $orderRepository
     * @param OrderService $orderService
     */
    public function __construct(
        OrderRepository $orderRepository,
        OrderService $orderService
    ) {
        parent::__construct();

        $this->orderRepository = $orderRepository;
        $this->orderService = $orderService;
    }

    /**
     * Returns list of orders
     *
     * @param DefaultListRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(DefaultListRequest $request)
    {
        $orders = $this->orderRepository->list($request->validated());

        return response()->pagination(OrderResource::collection($orders));
    }

    /**
     * View an order
     *
     * @param Order $order
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function view(Order $order)
    {
        return response()->success(new OrderResource($order->load(['supplier', 'user', 'offer', 'offer.audits'])));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function exportPDF(Request $request)
    {
        $target = $request->input('exportSettings.target');
        $format = $request->input('exportSettings.format');

        return $format === 'pdf'
            ? $this->orderService->generatePDFExport($target)
            : $this->orderService->generateExcelExport($target);
    }
}
