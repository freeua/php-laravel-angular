<?php

namespace App\Services\Companies;

use App\Exceptions\UserIsNotAllowed;
use App\Models\Companies\Company;
use App\Models\Rates\InsuranceRate;
use App\Models\Rates\ServiceRate;
use App\Portal\Models\Offer;

class LeasingRatesService
{
    function addInsuranceRate(Company $company, array $insuranceRate)
    {
        return $company->insuranceRates()->save(new InsuranceRate($insuranceRate));
    }

    function addServiceRate(Company $company, array $serviceRate)
    {
        return $company->serviceRates()->save(new ServiceRate($serviceRate));
    }

    function editInsuranceRate(Company $company, InsuranceRate $insuranceRate, array $data)
    {
        $insuranceRate->update($data);
        return $insuranceRate;
    }

    function editServiceRate(Company $company, ServiceRate $serviceRate, array $data)
    {
        $serviceRate->update($data);
        return $serviceRate;
    }

    function deleteInsuranceRate(Company $company, InsuranceRate $insuranceRate)
    {
        $insurance_data = $insuranceRate->getAttributes();
        $offers_ids = [];

        Offer::query()->where('insurance_rate_id', '=', $insurance_data['id'])->get()
            ->each(function (Offer $offer) {
                if ($offer->getAttribute('status_id') == 20) {
                    throw new UserIsNotAllowed();
                }
                $offer->setAttribute('insurance_rate_id', null);
                $offers_ids[] = $offer->getAttribute('id');
                $offer->saveOrFail();
            });
        $insuranceRate->delete();

        if (sizeof($offers_ids) > 0) {
            $new_insurance_data = InsuranceRate::query()
                ->where([
                    ['product_category_id', '=', $insurance_data['product_category_id']],
                    ['company_id', '=', $insurance_data['company_id']]
                ])
                ->firstOrFail();

            Offer::query()->whereIn('id', $offers_ids)->get()
                ->each(function (Offer $offer) use ($new_insurance_data) {
                    $offer->insurance_rate_id = $new_insurance_data->id;
                    $offer->saveOrFail();
                });
        }

        return $company->insuranceRates;
    }

    function deleteServiceRate(Company $company, ServiceRate $serviceRate)
    {
        $service_data = $serviceRate->getAttributes();
        $offers_ids = [];

        Offer::query()->where('service_rate_id', '=', $service_data['id'])->get()
            ->each(function (Offer $offer) {
                if ($offer->getAttribute('status_id') == 20) {
                    throw new UserIsNotAllowed();
                }
                $offer->setAttribute('service_rate_id', null);
                $offers_ids[] = $offer->getAttribute('id');
                $offer->saveOrFail();
            });
        $serviceRate->delete();

        if (sizeof($offers_ids) > 0) {
            $new_service_data = ServiceRate::query()
                ->where([
                    ['product_category_id', '=', $service_data['product_category_id']],
                    ['company_id', '=', $service_data['company_id']]
                ])
                ->firstOrFail();

            Offer::query()->whereIn('id', $offers_ids)->get()
                ->each(function (Offer $offer) use ($new_service_data) {
                    $offer->service_rate_id = $new_service_data->id;
                    $offer->saveOrFail();
                });
        }

        return $company->serviceRates;
    }
}
