<?php

namespace App\Http\Controllers\Offers;

use App\Helpers\StorageHelper;
use App\Http\Requests\Offers\CreateOfferRequest;
use App\Http\Resources\LeasingDocuments\OfferResource;
use App\Portal\Models\Offer;
use App\Services\Offers\OfferService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OfferController extends \Illuminate\Routing\Controller
{
    /** @var $offerService OfferService */
    public $offerService;
    public function __construct(OfferService $offerService)
    {
        $this->offerService = $offerService;
    }

    public function create(CreateOfferRequest $request)
    {
        $offer = $this->offerService->create($request);
        return response()->json(new OfferResource($offer));
    }

    public function edit(Offer $offer, CreateOfferRequest $request)
    {
        $offer = $this->offerService->edit($offer, $request);
        return response()->json(new OfferResource($offer));
    }

    public function view(Offer $offer)
    {
        return response()->json(new OfferResource($offer->load(['audits', 'status', 'user', 'supplier'])));
    }

    public function downloadPdf(Offer $offer)
    {
        $path = $offer->offerPdf;

        if (!empty($path) && StorageHelper::exists($path, StorageHelper::PRIVATE_DISK)) {
            return StorageHelper::downloadFromDisk($path, StorageHelper::PRIVATE_DISK);
        }

        throw new NotFoundHttpException('File not found');
    }
}
