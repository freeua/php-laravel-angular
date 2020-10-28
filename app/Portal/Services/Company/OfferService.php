<?php

declare(strict_types=1);

namespace App\Portal\Services\Company;

use App\Exceptions\ContractLimitReached;
use App\Exceptions\LeasingBudgetReached;
use App\Exceptions\UserIsNotAllowed;
use App\Exports\OffersExport;
use App\Models\Audit;
use App\Portal\Helpers\AuthHelper;
use App\Portal\Helpers\ContractPrices;
use App\Portal\Models\Offer;
use App\Portal\Models\Role;
use App\Portal\Notifications\LeasingBudget\LeasingBudgetLow;
use App\Portal\Notifications\Offer\OfferApprovedForEmployee;
use App\Portal\Notifications\Offer\OfferApprovedForPortalAdmin;
use App\Portal\Notifications\Offer\OfferApprovedForSupplier;
use App\Portal\Notifications\Offer\OfferRejectedForEmployee;
use App\Portal\Notifications\Offer\OfferRejectedForPortalAdmin;
use App\Portal\Notifications\Offer\OfferRejectedForSupplier;
use App\Portal\Notifications\Offer\OffersExported;
use App\Portal\Repositories\UserRepository;
use App\System\Repositories\UserRepository as SystemUserRepository;
use Brick\Math\RoundingMode;
use Brick\Money\Money;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Notification;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use Symfony\Component\HttpKernel\Exception\HttpException;

class OfferService
{
    const NOTIFY_ADMINS_WHEN_PERCENT = 30;

    public function approve(Offer $offer): void
    {
        try {
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
            Audit::offerApproved($offer);
            $updateResult = $offer->update([
                'status_id' => Offer::STATUS_CONTRACT_APPROVED, 'status_updated_at' => Carbon::now()
            ]);
            if (!$updateResult) {
                \DB::commit();
                throw new HttpException(JsonResponse::HTTP_INTERNAL_SERVER_ERROR, __('offer.accept.failed'));
            }

            \App\Portal\Services\OrderService::create($offer);
            \DB::commit();

            $this->notifyPortalAdmins($offer);

            if ($this->shouldAdministratorsBeNotified($offer)) {
                $systemAdmins = app(SystemUserRepository::class)->all();
                $percent = $offer->user->company->remaining_leasing_budget * 100 / $offer->user->company->leasing_budget;
                Notification::send($systemAdmins, (new LeasingBudgetLow($offer->user->company, $percent)));
            }
            $offer->user->notify(new OfferApprovedForEmployee($offer));
            if ($offer->supplier) {
                $offer->supplier->notify(new OfferApprovedForSupplier($offer));
                if (isset($offer->sender)) {
                    $offer->sender->notify(new OfferApprovedForSupplier($offer));
                }
            }
        } catch (\Exception $exception) {
            \DB::rollBack();
            throw $exception;
        }
    }

    public function notifyPortalAdmins($offer)
    {
        $portalAdmins = app(UserRepository::class)->findByRole(Role::ROLE_PORTAL_ADMIN, $offer->portal_id);
        \Notification::send($portalAdmins, new OfferApprovedForPortalAdmin($offer));
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

        $portalAdmins = app(UserRepository::class)->findByRole(Role::ROLE_PORTAL_ADMIN, $offer->portal_id);
        \Notification::send($portalAdmins, new OfferRejectedForPortalAdmin($offer));
    }

    public function generatePDFExport($target)
    {
        $user = AuthHelper::user();
        $company = AuthHelper::companyId();
        $fileName = 'angebote_exportiert_' . Carbon::now()->format('dmY_His') . '.pdf';

        $data['user'] = $user;
        $data['offers'] = Offer::where('company_id', $company)
            ->orderBy('created_at', 'desc')
            ->get();

        $pdf = PDF::loadView('portal.offer.export', $data);

        if ($target === 'email') {
            Notification::send($user, new OffersExported($pdf->output('', 'S'), $fileName, null));
            return response()->success();
        } elseif ($target === 'download') {
            $pdf = $pdf->output();
            $response = response($pdf, 200, [
                'Content-Type' => 'application/pdf',
                'Content-disposition' => 'inline; filename="' . $fileName . '"',
                'Cache-Control' => ' public, must-revalidate, max-age=0',
                'Pragma' => 'public',
                'X-Generator' => 'mPDF ' . \Mpdf\Mpdf::VERSION,
                'Expires' => 'Sat, 26 Jul 1997 05:00:00 GMT',
                'Last-Modified' => gmdate('D, d M Y H:i:s') . ' GMT',
            ]);
            return $response;
        } else {
            return response()->error('Unknown target type');
        }
    }

    public function generateExcelExport($target)
    {
        $user = AuthHelper::user();
        $company = AuthHelper::companyId();
        $fileName = 'angebote_exportiert_' . Carbon::now()->format('dmY_His') . '.xlsx';

        $data['user'] = $user;
        $data['offers'] = Offer::where('company_id', $company)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($target === 'email') {
            $path = Excel::download(new OffersExport(), $fileName)->getFile();
            Notification::send($user, new OffersExported(null, $fileName, $path));
            return response()->success();
        } elseif ($target === 'download') {
            return Excel::download(new OffersExport(), $fileName);
        } else {
            return response()->error('Unknown target type');
        }
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

    public function downloadSignedContract(Offer $offer)
    {
        $pathToFile = \Storage::disk('private')->getDriver()->getAdapter()->applyPathPrefix($offer->contract_file);
        return response()->download($pathToFile);
    }

    public function downloadOfferPdf(Offer $offer)
    {
        $pathToFile = \Storage::disk('private')->getDriver()->getAdapter()->applyPathPrefix($offer->offerPdf);
        return response()->download($pathToFile);
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

    public function checkContractLimits(Offer $offer): bool
    {
        $user = $offer->user;
        if ($user->individual_settings && $user->max_user_amount && $user->max_user_contracts) {
            $agreedPurchasePrice = Money::of($offer->agreedPurchasePrice, 'EUR', null, RoundingMode::HALF_UP);
            $accessoriesDiscountedPrice = Money::of($offer->accessoriesDiscountedPrice, 'EUR', null, RoundingMode::HALF_UP);
            return $user->max_user_contracts > $user->activeOrders()->count()
                && $agreedPurchasePrice
                    ->plus($accessoriesDiscountedPrice, RoundingMode::HALF_UP)
                    ->isLessThanOrEqualTo($user->max_user_amount);
        }
        $agreedPurchasePrice = Money::of($offer->agreedPurchasePrice, 'EUR', null, RoundingMode::HALF_UP);
        $accessoriesDiscountedPrice = Money::of($offer->accessoriesDiscountedPrice, 'EUR', null, RoundingMode::HALF_UP);
        return AuthHelper::company()->max_user_contracts > $user->activeOrders()->count()
            && $agreedPurchasePrice
                ->plus($accessoriesDiscountedPrice, RoundingMode::HALF_UP)
                ->isLessThanOrEqualTo(AuthHelper::company()->max_user_amount);
    }

    private static function checkLeasingBudget(Offer $offer): bool
    {
        return Money::of(AuthHelper::company()->remaining_leasing_budget, 'EUR', null, RoundingMode::HALF_UP)
            ->isGreaterThanOrEqualTo(Money::of(($offer->agreedPurchasePrice) / 1.19, 'EUR', null, RoundingMode::HALF_UP));
    }

    private function shouldAdministratorsBeNotified($offer)
    {
        $company = $offer->user->company;
        $company->refresh();
        return $company->remaining_leasing_budget * 100 / $company->leasing_budget <= self::NOTIFY_ADMINS_WHEN_PERCENT;
    }
}
