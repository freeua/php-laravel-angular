<?php
/**
 * Created by PhpStorm.
 * User: jpicornell
 * Date: 2018-12-08
 * Time: 18:09
 */

namespace App\Services\Orders;

use App\Helpers\StorageHelper;
use App\Portal\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ExportService
{
    public function getExportableOrders($request): Collection
    {
        $query = Order::query()
            ->where('status_id', Order::STATUS_SUCCESSFUL);
        if (isset($request['picked_up_at'])) {
            $date = Carbon::parse($request['picked_up_at']);
            $dateEnd = Carbon::parse($request['picked_up_at'])->add('1 day');
            $query->where('picked_up_at', '>=', $date)
                ->where('picked_up_at', '<=', $dateEnd);
        } else {
            $query->where('picked_up_at', '>=', Carbon::today());
        }

        return $query->get();
    }

    public function getOrder($name): Order
    {
        return Order::query()->where('number', $name)->firstOrFail();
    }

    public function markAsTransferred($orders)
    {
        return Order::query()->whereIn('id', $orders)->update(['transferred' => true]);
    }

    public function getSignedContract(Order $order): string
    {
        return base64_encode(StorageHelper::getFromDisk($order->offer->contract_file, StorageHelper::PRIVATE_DISK));
    }

    public function getTakeoverDocument(Order $order): string
    {
        return base64_encode(StorageHelper::getFromDisk($order->takeoverFile, StorageHelper::PRIVATE_DISK));
    }

    public function getSingleLeasingContract(Order $order): string
    {
        return base64_encode(StorageHelper::getFromDisk($order->singleLeasingFile, StorageHelper::PRIVATE_DISK));
    }
}
