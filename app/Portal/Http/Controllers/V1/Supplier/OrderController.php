<?php

namespace App\Portal\Http\Controllers\V1\Supplier;

use App\Helpers\StorageHelper;
use App\Http\Requests\DefaultListRequest;
use App\Leasings\Services\DocumentService;
use App\Portal\Helpers\AuthHelper;
use App\Portal\Http\Requests\V1\Supplier\PickupOrderRequest;
use App\Portal\Http\Requests\V1\Supplier\UploadInvoiceRequest;
use App\Portal\Http\Resources\V1\Supplier\ListCollections\OrderListCollection;
use App\Http\Resources\LeasingDocuments\OrderResource;
use App\Documents\Models\Document;
use App\Portal\Notifications\Order\OrderUploadInvoiceForSysAdmin;
use App\Portal\Repositories\Supplier\OrderRepository;
use App\Portal\Services\OrderService;
use App\Portal\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class OrderController
 *
 * @package App\Portal\Http\Controllers\V1\Supplier
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
        return response()->success(new OrderResource($order->load(['supplier', 'user', 'status', 'offer'])));
    }

    /**
     * Mark order as ready for pick up
     *
     * @param Order $order
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function ready(Order $order)
    {
        if (!$this->orderService->canBeReady($order)) {
            return response()->error([__('order.ready.invalid')], __('order.ready.invalid'), JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $result = $this->orderService->ready($order);

        return $result
            ? response()->success(new OrderResource($order->fresh()))
            : response()->error([__('order.ready.failed')], __('order.ready.failed'));
    }

    /**
     * Mark order as picked up
     *
     * @param Order $order
     * @param PickupOrderRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function pickup(Order $order, PickupOrderRequest $request)
    {
        $this->orderService->pickup($order, $request->validated());

        return response()->success(new OrderResource($order->fresh()));
    }

    public function uploadInvoice(Order $order, UploadInvoiceRequest $request)
    {
        DocumentService::uploadSupplierInvoice($order, $request->input('invoice_file'));
        return response()->success(new OrderResource($order->fresh()));
    }


    public function downloadInvoice(Order $order)
    {
        $path = $order->invoice_file;

        if (!empty($path) && StorageHelper::exists($path, StorageHelper::PRIVATE_DISK)) {
            return StorageHelper::downloadFromDisk($path, StorageHelper::PRIVATE_DISK);
        }
        throw new NotFoundHttpException('File not found');
    }

    public function handlePrivateJsonFile(string $base64File, string $folder, string $fileName, string $previousFileToWipe = null): string
    {
        if ($previousFileToWipe) {
            \Storage::disk('private')->delete($previousFileToWipe);
        }
        $fileContents = $this->decodeBase64($base64File);
        \Storage::disk('private')->put($folder . '/' . $fileName, $fileContents);
        return $folder . '/' . $fileName;
    }

    private function decodeBase64(string $base64)
    {
        return base64_decode(explode(',', substr($base64, 5), 2)[1]);
    }
}
