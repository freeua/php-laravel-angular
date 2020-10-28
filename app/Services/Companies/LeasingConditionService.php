<?php

namespace App\Services\Companies;

use App\Models\Companies\Company;
use App\Models\LeasingCondition;
use App\Models\Portal;
use App\Portal\Http\Requests\V1\LeasingConditionRequest;
use App\Portal\Notifications\Company\LeasingConditionChanged;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpKernel\Exception\HttpException;

class LeasingConditionService
{
    public function editPortalCondition(
        Portal $portal,
        LeasingCondition $leasingSetting,
        LeasingConditionRequest $request
    ) {
        if ($portal->id != $leasingSetting->portal_id) {
            throw new HttpException(422, 'Leasing condition is not associated to the portal');
        }
        $data = $request->validated();
        if (!$leasingSetting->default && isset($data['default']) && $data['default']) {
            $leasingSetting->makeDefault();
        }
        unset($data['default']);
        $leasingSetting->fill($data);
        $leasingSetting->save();
        return $leasingSetting;
    }

    public function createCompanyLeasingCondition(Company $company, array $validated): LeasingCondition
    {
        try {
            DB::beginTransaction();
            /** @var LeasingCondition $leasingCondition */
            $leasingCondition = $company->leasingConditions()->make($validated);
            $leasingCondition->deactivate();
            $saved = $company->leasingConditions()->save($leasingCondition);
            if (!$saved) {
                throw new HttpException(500, __('company.create_leasing.failed'));
            }
            DB::commit();
            return $leasingCondition;
        } catch (\Throwable $exception) {
            DB::rollBack();
            throw new HttpException(500, __('company.create_leasing.failed'));
        }
    }

    public function activateCompanyLeasingCondition(
        Company $company,
        LeasingCondition $leasingCondition
    ) {
        try {
            DB::beginTransaction();
            if ($leasingCondition->isActive()) {
                DB::rollBack();
                throw new HttpException(JsonResponse::HTTP_UNPROCESSABLE_ENTITY, __('company.leasing_already_active'));
            }
            $futureLeasing = $company
                ->futureLeasingConditionsByProductCategoryId($leasingCondition->product_category_id)
                ->first();
            if ($futureLeasing) {
                $futureLeasing->deactivate();
                $futureLeasing->save();
            }
            $activeLeasingCondition = $company
                ->activeLeasingConditionsByProductCategoryId($leasingCondition->product_category_id)->first();
            $activeLeasingCondition->deactivateTomorrow();
            $activeLeasingCondition->save();
            $leasingCondition->activate(Carbon::tomorrow());
            $leasingCondition->save();
            $this->notifyAdmins(
                new LeasingConditionChanged($company, \Auth::user(), $activeLeasingCondition, $leasingCondition)
            );
            DB::commit();
            return $leasingCondition;
        } catch (HttpException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    public function deactivateCompanyLeasingCondition(
        Company $company,
        LeasingCondition $leasingCondition
    ) {
        try {
            DB::beginTransaction();
            if ($leasingCondition->isActive()) {
                DB::rollBack();
                throw new HttpException(JsonResponse::HTTP_UNPROCESSABLE_ENTITY, __('company.leasing_already_active'));
            }
            $leasingCondition->deactivate();
            $leasingCondition->save();
            $activeLeasingCondition = $company
                ->activeLeasingConditionsByProductCategoryId($leasingCondition->product_category_id)->first();
            $activeLeasingCondition->cancelDeactivation();
            $activeLeasingCondition->save();
            $this->notifyAdmins(
                new LeasingConditionChanged($company, \Auth::user(), $leasingCondition, $activeLeasingCondition)
            );
            DB::commit();
            return $leasingCondition;
        } catch (HttpException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    public function deleteCompanyLeasingCondition(
        Company $company,
        LeasingCondition $leasingCondition
    ) {
        try {
            DB::beginTransaction();
            if ($leasingCondition->isActive()) {
                DB::rollBack();
                throw new HttpException(JsonResponse::HTTP_UNPROCESSABLE_ENTITY, __('company.leasing_already_active'));
            }
            if ($leasingCondition->isFuture()) {
                $activeLeasingCondition = $company
                    ->activeLeasingConditionsByProductCategoryId($leasingCondition->product_category_id)->first();
                $activeLeasingCondition->update(['inactive_at' => null]);
            }
            $leasingCondition->delete();
            $this->notifyAdmins(
                new LeasingConditionChanged($company, \Auth::user(), $leasingCondition, null)
            );
            DB::commit();
            return $leasingCondition;
        } catch (HttpException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    public function editCompanyLeasingCondition(
        Company $company,
        LeasingCondition $leasingCondition,
        array $validated
    ) {
        try {
            DB::beginTransaction();
            if ($leasingCondition->isActive()) {
                DB::rollBack();
                throw new HttpException(JsonResponse::HTTP_UNPROCESSABLE_ENTITY, __('company.leasing_already_active'));
            }
            $leasingCondition->update($validated);
            DB::commit();
            return $leasingCondition;
        } catch (HttpException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    public function notifyAdmins(Notification $notification)
    {
        $systemAdmins = \App\System\Models\User::query()->get();
        \Notification::send($systemAdmins, $notification);
    }
}
