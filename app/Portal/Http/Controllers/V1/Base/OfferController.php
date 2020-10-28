<?php

namespace App\Portal\Http\Controllers\V1\Base;

use App\Portal\Http\Controllers\Controller;
use App\Portal\Models\Offer;

/**
 * Class OfferController
 *
 * @package App\Portal\Http\Controllers\V1\Base
 */
class OfferController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function statuses()
    {
        return response()->success(Offer::getStatuses());
    }
}
