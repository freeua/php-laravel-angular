<?php

declare(strict_types=1);

namespace App\Portal\Services\Base;

use App\Exceptions\ContractLimitReached;
use App\Exceptions\UserIsNotAllowed;
use App\Models\Audit;
use App\Portal\Helpers\AuthHelper;
use App\Portal\Helpers\ContractPrices;
use App\Portal\Models\Offer;
use App\Portal\Notifications\Offer\OfferAcceptedForEmployee;
use App\Portal\Notifications\Offer\OfferAcceptedForSupplier;
use App\Portal\Notifications\Offer\OfferRejectedForEmployee;
use App\Portal\Notifications\Offer\OfferRejectedForSupplier;
use App\Portal\Repositories\Supplier\OfferRepository;
use App\Portal\Repositories\UserRepository;
use Brick\Math\RoundingMode;
use Brick\Money\Money;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

class OfferService
{

    /** @var OfferRepository */
    public $offerRepository;
    /** @var UserRepository */
    public $userRepository;

    public function __construct(
        OfferRepository $offerRepository,
        UserRepository $userRepository
    ) {
        $this->offerRepository = $offerRepository;
        $this->userRepository = $userRepository;
    }

    public function accept(Offer $offer)
    {

        if (!$this->canBeAcceptedRejected($offer)) {
            throw new HttpException(JsonResponse::HTTP_UNPROCESSABLE_ENTITY, __('offer.accept.invalid'));
        }

        if (!$this->checkContractLimits($offer)) {
            throw new ContractLimitReached();
        }

        if (!$this->checkLeasingBudget($offer)) {
            throw new LeasingBudgetReached();
        }

        if (AuthHelper::isEmployee() && $offer->company->is_accept_employee && !$offer->user->is_accept_offer) {
            throw new UserIsNotAllowed();
        }

        \DB::beginTransaction();
        if ($offer->status_id == Offer::STATUS_PENDING_APPROVAL) {
            Audit::offerApproved($offer);
        } elseif ($offer->status_id == Offer::STATUS_PENDING) {
            $this->updatePrices($offer);
            Audit::offerAccepted($offer);
        }
        $updateResult = $offer->update([
            'status_id' => Offer::STATUS_ACCEPTED, 'status_updated_at' => Carbon::now()
        ]);
        if (!$updateResult) {
            \DB::commit();
            throw new HttpException(JsonResponse::HTTP_INTERNAL_SERVER_ERROR, __('offer.accept.failed'));
        }

        \DB::commit();

        return $offer;
    }

    public function reject(Offer $offer)
    {
        \DB::beginTransaction();
        if ($offer->status_id == Offer::STATUS_PENDING_APPROVAL) {
            Audit::offerRejectedByCompany($offer, AuthHelper::user());
        } elseif ($offer->status_id == Offer::STATUS_PENDING) {
            Audit::offerRejectedByEmployee($offer, AuthHelper::user());
        }
        $offer->reject();
        $offer->saveOrFail();
        $offer->user->notify(new OfferRejectedForEmployee($offer));
        if (isset($offer->supplier)) {
            $offer->supplier->notify(new OfferRejectedForSupplier($offer));
        }
        if (isset($offer->sender)) {
            $offer->sender->notify(new OfferRejectedForSupplier($offer));
        }
        \DB::commit();
    }

    public function canBeAcceptedRejected(Offer $offer): bool
    {
        return $offer->status_id === Offer::STATUS_PENDING || $offer->status_id === Offer::STATUS_PENDING_APPROVAL;
    }

    public function canContractAdded(Offer $offer): bool
    {
        return $offer->status_id === Offer::STATUS_ACCEPTED && !$offer->order;
    }

    public function canContractGenerated(Offer $offer): bool
    {
        return $this->canContractAdded($offer) && $offer->user->hasAllContractFields();
    }

    public function getContractData(Offer $offer): array
    {
        $user = AuthHelper::user();
        $user->company->load('leasingSettings');

        $user->company->leasingConditions = $user->company
            ->activeLeasingConditionsByProductCategoryId($offer->productCategory->id)->get();

        return [
            'user' => $user,
            'offer' => $offer,
            'signatures' => [
                'date' => Carbon::now()->format('d/m/Y'),
                'company_admin' => $user->company->admins->first()->fullName
            ]
        ];
    }

    public function checkContractLimits(Offer $offer): bool
    {
        $user = $offer->user;
        if ($user->individual_settings && $user->max_user_amount && $user->max_user_contracts) {
            $agreedPurchasePrice = Money::of($offer->agreedPurchasePrice, 'EUR', null, RoundingMode::HALF_UP);
            $accessoriesDiscountedPrice = Money::of($offer->accessoriesDiscountedPrice, 'EUR', null, RoundingMode::HALF_UP);
            return $user->max_user_contracts > $user->acceptedOffers()->count()
                && $agreedPurchasePrice
                    ->plus($accessoriesDiscountedPrice, RoundingMode::HALF_UP)
                    ->isLessThanOrEqualTo($user->max_user_amount);
        }
        $agreedPurchasePrice = Money::of($offer->agreedPurchasePrice, 'EUR', null, RoundingMode::HALF_UP);
        $accessoriesDiscountedPrice = Money::of($offer->accessoriesDiscountedPrice, 'EUR', null, RoundingMode::HALF_UP);
        return AuthHelper::company()->max_user_contracts > $user->acceptedOffers()->count()
            && $agreedPurchasePrice
                ->plus($accessoriesDiscountedPrice, RoundingMode::HALF_UP)
                ->isLessThanOrEqualTo(AuthHelper::company()->max_user_amount);
    }

    private static function checkLeasingBudget(Offer $offer): bool
    {
        return Money::of(AuthHelper::company()->remaining_leasing_budget, 'EUR', null, RoundingMode::HALF_UP)
            ->isGreaterThanOrEqualTo(Money::of(($offer->agreedPurchasePrice) / 1.19, 'EUR', null, RoundingMode::HALF_UP));
    }

    public function updatePrices(Offer $offer)
    {
        $pricesHelper = new ContractPrices($offer);
        $offer->insuranceRateAmount = $pricesHelper->getInsuranceRate()->getAmount()->toFloat();
        $offer->serviceRateAmount = $pricesHelper->getServiceRate()->getAmount()->toFloat();
        $offer->leasingRateAmount = $pricesHelper->getLeasingRate()->getAmount()->toFloat();
        $offer->insuranceRateSubsidy = $pricesHelper->getInsuranceCoverage()->getAmount()->toFloat();
        $offer->serviceRateSubsidy = $pricesHelper->getServiceCoverage()->getAmount()->toFloat();
        $offer->leasingRateSubsidy = $pricesHelper->getLeasingRateCoverage()->getAmount()->toFloat();
        if (!$pricesHelper->getTotalRateWithCoverages()->isZero()) {
            $offer->taxRate = floor(($offer->productListPrice + $offer->accessoriesPrice) * 0.005);
        } else {
            $offer->taxRate = 0;
        }
        $offer->saveOrFail();
    }
}
