<?php

namespace App\Http\Controllers\Companies;

use App\Http\Requests\InsuranceRateRequest;
use App\Http\Requests\ServiceRateRequest;
use App\Http\Resources\LeasingSettings\RateResource;
use App\Models\Companies\Company;
use App\Models\Rates\InsuranceRate;
use App\Models\Rates\ServiceRate;
use App\Services\Companies\LeasingRatesService;
use Illuminate\Routing\Controller;

class CompanyLeasingRatesController extends Controller
{
    private $leasingRatesService;

    public function __construct(LeasingRatesService $leasingRatesService)
    {
        $this->leasingRatesService = $leasingRatesService;
    }

    public function addInsuranceRate(Company $company, InsuranceRateRequest $request)
    {
        return response()->json(
            RateResource::make(
                $this->leasingRatesService->addInsuranceRate($company, $request->validated())
            )
        );
    }

    public function addServiceRate(Company $company, ServiceRateRequest $request)
    {
        return response()->json(
            RateResource::make(
                $this->leasingRatesService->addServiceRate($company, $request->validated())
            )
        );
    }

    public function editInsuranceRate(Company $company, InsuranceRate $insuranceRate, InsuranceRateRequest $request)
    {
        return response()->json(
            RateResource::make(
                $this->leasingRatesService->editInsuranceRate($company, $insuranceRate, $request->validated())
            )
        );
    }

    public function editServiceRate(Company $company, ServiceRate $serviceRate, ServiceRateRequest $request)
    {
        return response()->json(
            RateResource::make(
                $this->leasingRatesService->editServiceRate($company, $serviceRate, $request->validated())
            )
        );
    }

    public function deleteInsuranceRate(Company $company, InsuranceRate $insuranceRate)
    {
        return response()->json(
            RateResource::collection(
                $this->leasingRatesService->deleteInsuranceRate($company, $insuranceRate)
            )
        );
    }

    public function deleteServiceRate(Company $company, ServiceRate $serviceRate)
    {
        return response()->json(
            RateResource::collection(
                $this->leasingRatesService->deleteServiceRate($company, $serviceRate)
            )
        );
    }
}
