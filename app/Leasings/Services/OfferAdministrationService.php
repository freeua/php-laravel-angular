<?php

declare(strict_types=1);

namespace App\Leasings\Services;

use App\Exceptions\ContractLimitReached;
use App\Exceptions\LeasingBudgetReached;
use App\Helpers\PortalHelper;
use App\Models\Audit;
use App\Portal\Helpers\AuthHelper;
use App\Portal\Models\Offer;
use App\Portal\Notifications\LeasingBudget\LeasingBudgetLow;
use App\Portal\Notifications\Offer\OfferRejectedForEmployee;
use App\Portal\Notifications\Offer\OfferRejectedForSupplier;
use App\System\Repositories\UserRepository as SystemUserRepository;
use Brick\Math\RoundingMode;
use Brick\Money\Money;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Notification;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Portal\Helpers\ContractPrices;

class OfferAdministrationService
{
    const NOTIFY_ADMINS_WHEN_PERCENT = 20;
    const NOTIFY_COMPANY_WHEN_PERCENT = 10;

    public function approve(Offer $offer)
    {

        if (!$this->canBeAcceptedRejected($offer)) {
            throw new HttpException(JsonResponse::HTTP_UNPROCESSABLE_ENTITY, __('offer.accept.invalid'));
        }

        if (!$this->checkLeasingBudget($offer)) {
            throw new LeasingBudgetReached();
        }

        if (!$this->checkContractLimits($offer)) {
            throw new ContractLimitReached();
        }

        \DB::beginTransaction();
        if ($offer->status_id == Offer::STATUS_PENDING_APPROVAL) {
            Audit::offerApproved($offer);
        } elseif ($offer->status_id == Offer::STATUS_PENDING) {
            Audit::offerAccepted($offer);
        }
        $this->updatePrices($offer);
        $updateResult = $offer->update([
            'status_id' => Offer::STATUS_CONTRACT_APPROVED, 'status_updated_at' => Carbon::now()
        ]);
        if (!$updateResult) {
            \DB::commit();
            throw new HttpException(JsonResponse::HTTP_INTERNAL_SERVER_ERROR, __('offer.accept.failed'));
        }

        if ($this->shouldAdministratorsBeNotified($offer)) {
            $systemAdmins = app(SystemUserRepository::class)->all();
            $percent = $offer->user->company->remaining_leasing_budget * 100 / $offer->user->company->leasing_budget;
            Notification::send(PortalHelper::getPortal(), new LeasingBudgetLow($offer->user->company, $percent));
            Notification::send($systemAdmins, (new LeasingBudgetLow($offer->user->company, $percent)));
        }

        if ($this->shouldCompanyBeNotified($offer)) {
            $percent = $offer->user->company->remaining_leasing_budget * 100 / $offer->user->company->leasing_budget;
            Notification::send($offer->user->company, (new LeasingBudgetLow($offer->user->company, $percent)));
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
        $offer->supplier->notify(new OfferRejectedForSupplier($offer));
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

    public function shouldAdministratorsBeNotified(Offer $offer)
    {
        $company = $offer->user->company;
        $company->refresh();
        return $company->remaining_leasing_budget * 100 / $company->leasing_budget < $this::NOTIFY_ADMINS_WHEN_PERCENT;
    }

    public function shouldCompanyBeNotified(Offer $offer)
    {
        $company = $offer->user->company;
        $company->refresh();
        return $company->remaining_leasing_budget * 100 / $company->leasing_budget < $this::NOTIFY_COMPANY_WHEN_PERCENT;
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

    public function checkLeasingBudget(Offer $offer): bool
    {
        return AuthHelper::company()->remaining_leasing_budget >= $offer->productDiscountedPrice + $offer->accessoriesPrice;
    }

    public function updatePrices(Offer $offer)
    {
        $pricesHelper = new ContractPrices($offer);
        if (!$pricesHelper->getTotalRateWithCoverages()->isZero()) {
            $offer->taxRate = floor(($offer->productListPrice + $offer->accessoriesPrice) * 0.005);
            $offer->saveOrFail();
        }
    }
}
