<?php
namespace App\Leasings\Controllers;

use App\Helpers\StorageHelper;
use App\Leasings\Requests\CreditNoteReadRequest;
use App\Leasings\Resources\OrderMarkedAsReadyResource;
use App\Leasings\Resources\OrderPickedUpResource;
use Illuminate\Routing\Controller;
use App\Leasings\Resources\OrderResource;
use App\Leasings\Services\OrderService;
use App\Leasings\Requests\OrderListRequest;
use App\Portal\Models\Order;
use App\Leasings\Requests\OrderRequest;
use App\Leasings\Requests\OrderReadyRequest;
use App\Leasings\Requests\OrderPickUpRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OrderController extends Controller
{
    public function list(OrderListRequest $request)
    {
        return response()->json(OrderResource::collection(
            OrderService::list()
        ));
    }

    public function get(Order $order, OrderRequest $request)
    {
        return response()->json(OrderResource::make($order));
    }

    public function downloadCreditNote(Order $order)
    {
        if ($order->creditNoteFile) {
            return StorageHelper::download($order->creditNoteFile);
        } else {
            throw new NotFoundHttpException('Order has not a credit note');
        }
    }

    public function markAsReady(Order $order, OrderReadyRequest $request)
    {
        $orderChanged = OrderService::markAsReady($order);
        return response()->json(OrderMarkedAsReadyResource::make($orderChanged));
    }

    public function markCreditNoteRead(Order $order, CreditNoteReadRequest $request)
    {
        $orderChanged = OrderService::markCreditNoteRead($order);
        return response()->json(OrderPickedUpResource::make($orderChanged));
    }


    public function pickUp(Order $order, OrderPickUpRequest $request)
    {
        $orderChanged = OrderService::pickUp($order, $request->validated());
        return response()->json(OrderPickedUpResource::make($orderChanged));
    }
}
