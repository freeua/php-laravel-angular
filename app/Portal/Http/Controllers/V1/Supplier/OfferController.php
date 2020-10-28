<?php

namespace App\Portal\Http\Controllers\V1\Supplier;

use App\Http\Requests\DefaultListRequest;
use App\Models\Status;
use App\Models\Unit;
use App\Portal\Http\Requests\V1\Supplier\CreateOfferRequest;
use App\Http\Resources\LeasingDocuments\OfferResource;
use App\Portal\Models\Offer;
use App\Portal\Repositories\Supplier\OfferRepository;
use App\Portal\Services\Supplier\OfferService;

/**
 * Class OfferController
 *
 * @package App\Portal\Http\Controllers\V1\Supplier
 */
class OfferController extends \App\Portal\Http\Controllers\V1\Base\OfferController
{
    /** @var OfferRepository */
    private $offerRepository;

    /**
     * OfferController constructor.
     *
     * @param OfferRepository $offerRepository
     * @param OfferService    $offerService
     */
    public function __construct(OfferRepository $offerRepository)
    {
        parent::__construct();

        $this->offerRepository = $offerRepository;
    }

    /**
     * Returns list of offers
     *
     * @param DefaultListRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(DefaultListRequest $request)
    {
        $offers = $this->offerRepository->list($request->validated());

        return response()->pagination(OfferResource::collection($offers));
    }

    /**
     * View an offer
     *
     * @param Offer $offer
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function view(Offer $offer)
    {
        return response()->success(new OfferResource($offer->load('audits')));
    }

    public function getUnits()
    {
        return response()->success(Unit::query()->get());
    }

    public function statuses()
    {
        $statuses = Status::query()
            ->where('table', 'offers')
            ->whereNotIn('id', [
                Offer::STATUS_ACCEPTED,
                Offer::STATUS_PENDING_APPROVAL,
                Offer::STATUS_DRAFT,
            ])->get();
        return response()->success($statuses);
    }
}
