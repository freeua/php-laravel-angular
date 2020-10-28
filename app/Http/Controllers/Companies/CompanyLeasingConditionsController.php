<?php

namespace App\Http\Controllers\Companies;

use App\Models\Companies\Company;
use App\Models\LeasingCondition;
use App\Portal\Http\Requests\V1\LeasingConditionRequest;
use App\Portal\Http\Resources\V1\CompanyLeasingSettingResource;
use App\Services\Companies\LeasingConditionService;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CompanyLeasingConditionsController extends Controller
{
    /* @var LeasingConditionService */
    private $leasingConditionService;

    public function __construct(
        LeasingConditionService $leasingConditionService
    ) {
        $this->leasingConditionService = $leasingConditionService;
    }

    public function editLeasingCondition(
        Company $company,
        LeasingCondition $leasingCondition,
        LeasingConditionRequest $request
    ) {
        try {
            $leasingUpdated = $this->leasingConditionService
                ->editCompanyLeasingCondition($company, $leasingCondition, $request->validated());
            return response()->json(new CompanyLeasingSettingResource($leasingUpdated));
        } catch (HttpException $exception) {
            return response()->error(
                [$exception->getMessage()],
                $exception->getMessage(),
                $exception->getStatusCode()
            );
        }
    }

    public function addLeasingCondition(
        Company $company,
        LeasingConditionRequest $request
    ) {
        try {
            $leasingUpdated = $this->leasingConditionService
                ->createCompanyLeasingCondition($company, $request->validated());
            return response()->json(new CompanyLeasingSettingResource($leasingUpdated));
        } catch (HttpException $exception) {
            return response()->error(
                [$exception->getMessage()],
                $exception->getMessage(),
                $exception->getStatusCode()
            );
        }
    }

    public function deleteLeasingCondition(
        Company $company,
        LeasingCondition $leasingCondition
    ) {
        try {
            $leasingUpdated = $this->leasingConditionService
                ->deleteCompanyLeasingCondition($company, $leasingCondition);
            return response()->json(new CompanyLeasingSettingResource($leasingUpdated));
        } catch (HttpException $exception) {
            return response()->error(
                [$exception->getMessage()],
                $exception->getMessage(),
                $exception->getStatusCode()
            );
        }
    }

    public function activateLeasingCondition(
        Company $company,
        LeasingCondition $leasingCondition
    ) {
        try {
            $leasingUpdated = $this->leasingConditionService
                ->activateCompanyLeasingCondition($company, $leasingCondition);
            return response()->json(new CompanyLeasingSettingResource($leasingUpdated));
        } catch (HttpException $exception) {
            return response()->error(
                [$exception->getMessage()],
                $exception->getMessage(),
                $exception->getStatusCode()
            );
        }
    }

    public function deactivateLeasingCondition(
        Company $company,
        LeasingCondition $leasingCondition
    ) {
        try {
            $leasingUpdated = $this->leasingConditionService
                ->deactivateCompanyLeasingCondition($company, $leasingCondition);
            return response()->json(new CompanyLeasingSettingResource($leasingUpdated));
        } catch (HttpException $exception) {
            return response()->error(
                [$exception->getMessage()],
                $exception->getMessage(),
                $exception->getStatusCode()
            );
        }
    }
}
