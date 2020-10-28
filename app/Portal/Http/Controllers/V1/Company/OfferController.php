<?php

namespace App\Portal\Http\Controllers\V1\Company;

use App\Http\Requests\DefaultListRequest;
use App\Http\Resources\LeasingDocuments\OfferResource;
use App\Portal\Http\Resources\V1\Company\SupplierOfferResource;
use App\Portal\Http\Resources\V1\Company\UserOfferResource;
use App\Portal\Models\Offer;
use App\Portal\Models\Supplier;
use App\Portal\Models\User;
use App\Portal\Repositories\Company\OfferRepository;
use App\Portal\Services\Company\OfferService;
use Illuminate\Http\Request;

class OfferController extends \App\Portal\Http\Controllers\V1\Base\OfferController
{
    /** @var OfferService */
    private $offerService;
    /** @var OfferRepository */
    private $offerRepository;

    public function __construct(
        OfferRepository $offerRepository,
        OfferService $offerService
    ) {
        parent::__construct();

        $this->offerService = $offerService;
        $this->offerRepository = $offerRepository;
    }

    public function index(DefaultListRequest $request)
    {
        $offers = $this->offerRepository->list($request->validated());

        return response()->pagination(OfferResource::collection($offers));
    }

    public function view(Offer $offer)
    {
        return response()->success(new OfferResource($offer->load(['audits', 'status', 'user', 'supplier'])));
    }

    public function approve(Offer $offer)
    {
        $this->offerService->approve($offer);
        return response()->json([
            'orderId' => $offer->order->id,
        ]);
    }

    public function reject(Offer $offer)
    {
        $this->offerService->reject($offer);
        return response()->success(new OfferResource($offer));
    }

    public function exportPDF(Request $request)
    {
        $target = $request->input('exportSettings.target');
        $format = $request->input('exportSettings.format');

        return $format === 'pdf'
            ? $this->offerService->generatePDFExport($target)
            : $this->offerService->generateExcelExport($target);
    }

    public function downloadSignedContract(Offer $offer)
    {
        return $this->offerService->downloadSignedContract($offer);
    }

    public function userOffers(User $user, DefaultListRequest $request)
    {

        $offers = $this->offerRepository->userList($user, $request->validated());

        return response()->pagination(UserOfferResource::collection($offers));
    }

    public function supplierOffers(Supplier $supplier, DefaultListRequest $request)
    {

        $offers = $this->offerRepository->suppliersList($supplier, $request->validated());

        return response()->pagination(SupplierOfferResource::collection($offers));
    }

    public function downloadOfferPdf(Offer $offer)
    {
        return $this->offerService->downloadOfferPdf($offer);
    }
}
