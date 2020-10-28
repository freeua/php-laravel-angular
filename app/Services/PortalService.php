<?php

namespace App\Services;

use App\Http\Requests\InsuranceRateRequest;
use App\Http\Requests\ServiceRateRequest;
use App\Models\Companies\Company;
use App\Models\LeasingCondition;
use App\Models\Portal;
use App\Models\Rates\InsuranceRate;
use App\Models\Rates\ServiceRate;
use App\Portal\Http\Requests\V1\LeasingConditionRequest;
use App\Portal\Models\User;
use App\System\Repositories\PortalLeasingSettingRepository;
use App\System\Repositories\PortalRepository;
use App\Traits\UploadsFile;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PortalService
{
    use UploadsFile;
    /** @var PortalLeasingSettingRepository */
    private $leasingSettingRepository;
    /** @var PortalRepository */
    private $portalRepository;

    public function __construct(
        PortalRepository $portalRepository,
        PortalLeasingSettingRepository $leasingSettingsRepository
    ) {
        $this->portalRepository = $portalRepository;
        $this->leasingSettingRepository = $leasingSettingsRepository;
    }
    public function create(array $data)
    {
        \DB::beginTransaction();
        $portal = new Portal;
        $portal->domain = $data['subdomain'] . '.' . env('APP_URL_BASE');
        $portal->fill($data);
        $portal->save();
        \DB::commit();

        return $portal;
    }


    public function update(Portal $portal, array $data)
    {
        if (is_object($data['leasingablePdf'])) {
            unset($data['leasingablePdf']);
        }
        if (is_object($data['servicePdf'])) {
            unset($data['servicePdf']);
        }
        if (is_object($data['policyPdf'])) {
            unset($data['policyPdf']);
        }
        if (is_object($data['imprintPdf'])) {
            unset($data['imprintPdf']);
        }
        if (is_object($data['logo'])) {
            unset($data['logo']);
        }
        $portal->fill($data);
        $portal->domain = $data['subdomain'] . '.' . env('APP_URL_BASE');
        \Cache::delete("portals.{$portal->domain}");
        $portal->save();

        //Update companies subsidy
        $companies = Company::where('portal_id', '=', $portal->id)
            ->where('uses_default_subsidies', '=', true)
            ->get();
        if (!empty($companies)) {
            foreach ($companies as $company) {
                $company->update([
                    'insurance_covered' => $portal->insurance_rate_subsidy,
                    'insurance_covered_type' => $portal->insurance_rate_subsidy_type,
                    'insurance_covered_amount' => $portal->insurance_rate_subsidy_amount,
                    'leasing_rate' => $portal->leasing_rate_subsidy,
                    'leasing_rate_type' => $portal->leasing_rate_subsidy_type,
                    'leasing_rate_amount' => $portal->leasing_rate_subsidy_amount,
                    'maintenance_covered' => $portal->service_rate_subsidy,
                    'maintenance_covered_type' => $portal->service_rate_subsidy_type,
                    'maintenance_covered_amount' => $portal->service_rate_subsidy_amount,
                ]);

                $company->users()->update([
                    'individual_settings' => false
                ]);
            }
        }

        return $portal;
    }

    public function addInsuranceRate(Portal $portal, InsuranceRateRequest $request)
    {
        $validatedRequest = $request->validated();
        $insuranceRate = new InsuranceRate($validatedRequest);
        $insuranceRate->productCategory()->associate($validatedRequest['productCategory']['id']);
        $portal->insuranceRates()->save($insuranceRate);
        return $insuranceRate;
    }

    public function addServiceRate(Portal $portal, ServiceRateRequest $request)
    {
        $validatedRequest = $request->validated();
        $serviceRate = new ServiceRate($validatedRequest);
        $serviceRate->productCategory()->associate($validatedRequest['productCategory']['id']);
        $portal->serviceRates()->save($serviceRate);
        return $serviceRate;
    }

    public function addLeasingCondition(Portal $portal, LeasingConditionRequest $request)
    {
        $validatedRequest = $request->validated();
        return $portal->leasingConditions()->save(new LeasingCondition($validatedRequest));
    }

    public function editInsuranceRate(Portal $portal, InsuranceRate $insuranceRate, array $data)
    {
        if ($portal->id === $insuranceRate->portal_id) {
            $insuranceRate->update($data);
            if (isset($data['default']) && $data['default']) {
                $insuranceRate->makeDefault();
            }
        } else {
            throw new HttpException(422, 'Insurance rate is not associated to the portal');
        }
    }

    public function editServiceRate(Portal $portal, ServiceRate $serviceRate, array $data)
    {
        if ($portal->id === $serviceRate->portal_id) {
            $serviceRate->update($data);
            if (isset($data['default']) && $data['default']) {
                $serviceRate->makeDefault();
            }
        } else {
            throw new HttpException(422, 'Insurance rate is not associated to the portal');
        }
    }

    public function editLeasingCondition(
        Portal $portal,
        LeasingCondition $portalLeasingCondition,
        LeasingConditionRequest $request
    ) {
        if ($portal->id === $portalLeasingCondition->portal_id) {
            $portalLeasingCondition->update($request->validated());
        } else {
            throw new HttpException(422, 'Insurance rate is not associated to the portal');
        }
    }

    public function deleteInsuranceRate(Portal $portal, InsuranceRate $rate)
    {
        if ($portal->id === $rate->portal_id) {
            $rate->delete();
        } else {
            throw new HttpException(422, 'Insurance rate is not associated to the portal');
        }
    }

    public function deleteServiceRate(Portal $portal, ServiceRate $rate)
    {
        if ($portal->id === $rate->portal_id) {
            $rate->delete();
        } else {
            throw new HttpException(422, 'Insurance rate is not associated to the portal');
        }
    }

    public function deleteLeasingCondition(Portal $portal, LeasingCondition $leasingCondition)
    {
        if ($portal->id === $leasingCondition->portal_id) {
            $leasingCondition->delete();
        } else {
            throw new HttpException(422, 'Leasing condition is not associated to the portal');
        }
    }
}
