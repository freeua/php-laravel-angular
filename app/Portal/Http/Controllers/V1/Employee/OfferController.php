<?php

namespace App\Portal\Http\Controllers\V1\Employee;

use App\Http\Requests\DefaultListRequest;
use App\Http\Resources\LeasingSettings\RateResource;
use App\Models\Companies\Company;
use App\Portal\Http\Requests\V1\Employee\ChangeRatesRequest;
use App\Portal\Http\Requests\V1\Employee\GenerateOfferContractRequest;
use App\Portal\Http\Requests\V1\Employee\UploadOfferContractRequest;
use App\Portal\Http\Resources\V1\CompanyLeasingSettingResource;
use App\Portal\Http\Resources\V1\Employee\CompanyResource;
use App\Http\Resources\LeasingDocuments\OfferResource;
use App\Portal\Http\Resources\V1\UserResource;
use App\Documents\Models\Document;
use App\Portal\Models\Offer;
use App\Portal\Notifications\Offer\OfferSignedForEmployee;
use App\Portal\Repositories\Employee\OfferRepository;
use App\Portal\Services\Employee\OfferService;
use App\Portal\Services\OrderService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

class OfferController extends \App\Portal\Http\Controllers\V1\Base\OfferController
{
    /** @var OfferRepository */
    private $offerRepository;
    /** @var OfferService */
    private $offerService;

    public function __construct(
        OfferRepository $offerRepository,
        OfferService $offerService
    ) {
        parent::__construct();

        $this->offerRepository = $offerRepository;
        $this->offerService = $offerService;
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

    public function accept(Offer $offer)
    {
        try {
            $this->offerService->accept($offer);

            return response()->success();
        } catch (HttpException $exception) {
            return response()->error([$exception->getMessage()], $exception->getMessage(), $exception->getStatusCode());
        }
    }

    public function reject(Offer $offer)
    {
        if (!$this->offerService->canBeAcceptedRejected($offer)) {
            return response()->error([__('offer.reject.invalid')], __('offer.reject.invalid'), JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
        $this->offerService->reject($offer);

        return  response()->success();
    }

    public function getContractData(Offer $offer)
    {
        if (!$this->offerService->canContractAdded($offer)) {
            return response()->error(
                [__('offer.contract.generate.invalid')],
                __('offer.contract.generate.invalid'),
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $data = $this->offerService->getContractData($offer);

        return response()->success([
            'user' => new UserResource($data['user']),
            'company' => new CompanyResource($data['company']),
            'signatures' => $data['signatures'],
            'offer' => new OfferResource($data['offer']),
            'leasingCondition' => new CompanyLeasingSettingResource($data['leasingCondition']),
            'insuranceRates' => RateResource::collection($data['insuranceRates']),
            'serviceRates' => RateResource::collection($data['serviceRates']),
        ]);
    }

    public function generateContractPdf(Offer $offer, GenerateOfferContractRequest $request)
    {
        if (!$this->offerService->canContractAdded($offer)) {
            return response()->error([__('offer.contract.invalid')], __('offer.contract.invalid'), JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->offerService->generateContract($offer, $request->validated());
    }

    public function generateContract(Offer $offer, UploadOfferContractRequest $request)
    {
        $autoApproved = !Company::find($offer->company_id)->manual_contract_approval;

        if (!$this->offerService->canContractGenerated($offer)) {
            return response()->error([__('offer.contract.generate.invalid')], __('offer.contract.generate.invalid'), JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $pdfUploaded = $this->offerService->uploadContractPdf($offer, $request->file('file'));

        if (!$pdfUploaded) {
            return response()->error([__('Error uploading file')], __('Error uploading file'));
        }

        $offer->user->notify(new OfferSignedForEmployee($offer));
        if ($autoApproved) {
            $result = OrderService::create($offer);
            return $result
                ? response()->success()
                : response()->error([__('offer.contract.generate.failed')], __('offer.contract.generate.failed'));
        }

        $result = $this->offerService->setPending($offer);

        return $result
            ? response()->success()
            : response()->error([__('offer.status.change.failed')], __('offer.status.change.failed'));
    }

    public function changeRates(Offer $offer, ChangeRatesRequest $request)
    {
        $this->offerService->changeRates($offer, $request->validated());
        return response()->json(OfferResource::make($offer->refresh()));
    }
}
