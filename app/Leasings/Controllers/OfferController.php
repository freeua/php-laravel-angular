<?php
namespace App\Leasings\Controllers;

use Illuminate\Routing\Controller;
use App\Leasings\Requests\CreateOfferRequest;
use App\Leasings\Services\OfferService;
use App\Leasings\Resources\OfferResource;
use App\Leasings\Resources\OfferListResource;
use App\Portal\Models\Offer;
use App\Leasings\Requests\OfferRequest;
use App\Leasings\Requests\OfferListRequest;

class OfferController extends Controller
{
    public $offerService;

    public function __construct(OfferService $offerService)
    {
        $this->offerService = $offerService;
    }

    public function list(OfferListRequest $request)
    {
        return response()->json(
            OfferListResource::collection($this->offerService->list()),
        );
    }

    public function get(Offer $offer, OfferRequest $request)
    {
        return response()->json(OfferResource::make($offer));
    }

    public function create(CreateOfferRequest $request)
    {
        return response()->json(
            OfferResource::make($this->offerService->create($request))
        );
    }
}
