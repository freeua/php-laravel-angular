<?php

namespace App\Observers;

use App\Portal\Models\Order;

class OrderObserver
{
    public function created(Order $order)
    {
        $order->number = $order->generateCode($order->id, 7, '', 'DRA-');
        $order->save();
    }
}
