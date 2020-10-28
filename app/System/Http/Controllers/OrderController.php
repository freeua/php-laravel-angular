<?php

namespace App\System\Http\Controllers;

use App\Helpers\StorageHelper;
use App\Http\Requests\DefaultListRequest;
use App\Http\Resources\LeasingDocuments\ContractResource;
use App\System\Http\Requests\UpdateOrderRequest;
use App\System\Http\Resources\ListCollections\OrderListCollection;
use App\Http\Resources\LeasingDocuments\OrderResource;
use App\Portal\Models\Order;
use App\Repositories\OrderRepository;
use App\System\Services\OrderService;
use Maatwebsite\Excel\Excel;
use App\System\Exports\OrderExport;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class OrderController
 *
 * @package App\System\Http\Controllers
 */
class OrderController extends Controller
{
    /** @var OrderRepository */
    private $orderRepository;
    /** @var OrderService */
    private $orderService;

    /**
     * OrderController constructor.
     *
     * @param OrderRepository $orderRepository
     * @param OrderService    $orderService
     */
    public function __construct(OrderRepository $orderRepository, OrderService $orderService)
    {
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
        $portals = $this->orderRepository->list($request->validated());

        return response()->pagination(OrderResource::collection($portals));
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function view($id)
    {
        $order = $this->orderService->getOrder($id);

        return $order
            ? response()->success(new OrderResource($order->load(['supplier', 'user', 'city', 'offer.audits'])))
            : response()->error([__('order.view.failed')], __('order.view.failed'));
    }

    /**
     * @param Order              $order
     * @param UpdateOrderRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Order $order, UpdateOrderRequest $request)
    {
        $order = $this->orderService->update($order, $request->validated());

        return $order
            ? response()->success(new OrderResource($order))
            : response()->error([__('order.update.failed')], __('order.update.failed'));
    }

    /**
     * Export order
     *
     * @param Order $order
     * @param Excel $excel
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function export(Order $order, Excel $excel)
    {
        $export = new OrderExport($order);

        return $excel->download($export, 'Order #' . $order->number . '.xlsx', Excel::XLSX);
    }

    /**
     * @param Order $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function convert(Order $order)
    {
        if ($order->status == Order::STATUS_SUCCESSFUL) {
            return response()->error([__('order.make_contract.exists')], __('order.make_contract.exists'));
        }

        $contract = $this->orderService->convert($order);

        return $contract
            ? response()->success(new ContractResource($contract))
            : response()->error([__('order.make_contract.failed')], __('order.make_contract.failed'));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function statuses()
    {
        return response()->success(Order::getStatuses());
    }


    public function downloadInvoice(Order $order)
    {
        $path = $order->invoice_file;

        if (!empty($path) && StorageHelper::exists($path, StorageHelper::PRIVATE_DISK)) {
            return StorageHelper::downloadFromDisk($path, StorageHelper::PRIVATE_DISK);
        }
        throw new NotFoundHttpException('File not found');
    }
}
