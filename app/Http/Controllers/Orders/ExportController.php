<?php

namespace App\Http\Controllers\Orders;

use App\Http\Requests\Orders\ExportRequest;
use App\Http\Requests\Orders\MarkTransferredRequest;
use App\Http\Resources\Orders\ExportedOrder;
use App\Http\Resources\Orders\ExportedOrdersList;
use App\Portal\Models\Order;
use App\Services\Orders\ExportService;
use Illuminate\Routing\Controller;

class ExportController extends Controller
{
    public $exportService;
    public function __construct(ExportService $exportService)
    {
        $this->exportService = $exportService;
    }

    public function getOrders(ExportRequest $request)
    {
        $validatedRequest = $request->validated();
        if (isset($validatedRequest['mlfinvoices'])) {
            return new ExportedOrdersList($this->exportService->getExportableOrders($validatedRequest));
        } else {
            return new ExportedOrder($this->exportService->getOrder($validatedRequest['name']));
        }
    }

    public function markAsTransferred(MarkTransferredRequest $request)
    {
        $this->exportService->markAsTransferred($request->validated());
    }

    public function getSignedContract(Order $order)
    {
        return $this->exportService->getSignedContract($order);
    }

    public function getTakeoverDocument(Order $order)
    {
        return $this->exportService->getTakeoverDocument($order);
    }

    public function getSingleLeasingContract(Order $order)
    {
        return $this->exportService->getSingleLeasingContract($order);
    }
}
