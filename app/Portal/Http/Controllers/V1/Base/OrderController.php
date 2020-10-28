<?php

namespace App\Portal\Http\Controllers\V1\Base;

use App\Portal\Http\Controllers\Controller;
use App\Portal\Models\Order;

/**
 * Class OrderController
 *
 * @package App\Portal\Http\Controllers\V1\Base
 */
class OrderController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function statuses()
    {
        return response()->success(Order::getStatuses());
    }
}
