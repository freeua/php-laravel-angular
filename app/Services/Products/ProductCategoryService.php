<?php

namespace App\Services\Products;

use App\Http\Requests\InsuranceRateRequest;
use App\Http\Requests\ServiceRateRequest;
use App\Models\Companies\Company;
use App\Models\LeasingCondition;
use App\Models\Portal;
use App\Models\ProductCategory;
use App\Models\Rates\InsuranceRate;
use App\Models\Rates\ServiceRate;
use App\Portal\Http\Requests\V1\LeasingConditionRequest;
use App\Portal\Models\CompanyProductCategory;
use App\Traits\Paginates;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ProductCategoryService
{
    use Paginates;
    public function list(array $params, array $relationships = [])
    {
        $query = ProductCategory::query();
        if (!empty($params['status_id'])) {
            $query->where(['status_id' => $params['status_id']]);
        }

        return $this->processList($query, $params, $relationships);
    }

    public function all()
    {
        return ProductCategory::all();
    }

    public function create(array $data)
    {
        \DB::beginTransaction();
        $productCategory = ProductCategory::create($data);
        Portal::all()->each(function (Portal $portal) use ($productCategory) {
            $productCategory->insuranceRates->each(function (InsuranceRate $insuranceRate) use ($portal) {
                $portal->insuranceRates()->create($insuranceRate->toArray());
            });
            $productCategory->serviceRates->each(function (ServiceRate $serviceRate) use ($portal) {
                $portal->serviceRates()->create($serviceRate->toArray());
            });
            $productCategory->leasingConditions->each(function (LeasingCondition $leasingCondition) use ($portal) {
                $portal->leasingConditions()->create($leasingCondition->toArray());
            });
        });
        Company::all()->each(function (Company $company) use ($productCategory) {
            $productCategory->insuranceRates->each(function (InsuranceRate $insuranceRate) use ($company) {
                $company->insuranceRates()->create($insuranceRate->toArray());
            });
            $productCategory->serviceRates->each(function (ServiceRate $serviceRate) use ($company) {
                $company->serviceRates()->create($serviceRate->toArray());
            });
            $productCategory->leasingConditions->each(function (LeasingCondition $leasingCondition, $key) use ($company) {
                if ($key === 0) {
                    $leasingCondition->activate(Carbon::now());
                } else {
                    $leasingCondition->deactivate();
                }
                $company->leasingConditions()->create($leasingCondition->toArray());
            });
            $companyProductCategory = new CompanyProductCategory([
                'company_id' => $company->id,
                'category_id' => $productCategory->id,
                'status' => true
            ]);

            $companyProductCategory->saveOrFail();
        });
        \DB::commit();

        return $productCategory;
    }

    public function edit(ProductCategory $category, array $data)
    {
        $category->fill($data);
        $category->saveOrFail();
        return $category;
    }

    public function delete(ProductCategory $productCategory)
    {
        $productCategory->delete();
    }


    public function addInsuranceRate(ProductCategory $productCategory, InsuranceRateRequest $request)
    {
        $validatedRequest = $request->validated();
        $insuranceRate = new InsuranceRate($validatedRequest);
        $insuranceRate->productCategory()->associate($productCategory);
        $productCategory->insuranceRates()->save($insuranceRate);
        return $insuranceRate;
    }

    public function addServiceRate(ProductCategory $productCategory, ServiceRateRequest $request)
    {
        $validatedRequest = $request->validated();
        $serviceRate = new ServiceRate($validatedRequest);
        $serviceRate->productCategory()->associate($productCategory);
        $productCategory->serviceRates()->save($serviceRate);
        return $serviceRate;
    }

    public function addLeasingCondition(ProductCategory $productCategory, LeasingConditionRequest $request)
    {
        $validatedRequest = $request->validated();
        return $productCategory->leasingConditions()->save(new LeasingCondition($validatedRequest));
    }

    public function editInsuranceRate(ProductCategory $productCategory, InsuranceRate $insuranceRate, InsuranceRateRequest $request)
    {
        if ($productCategory->id === $insuranceRate->productCategoryId) {
            $validatedRequest = $request->validated();
            $insuranceRate->update($validatedRequest);
            if (isset($validatedRequest['default']) && $validatedRequest['default']) {
                $insuranceRate->makeDefault();
            }
        } else {
            throw new HttpException(422, 'Insurance rate is not associated to the portal');
        }
    }

    public function editServiceRate(ProductCategory $productCategory, ServiceRate $serviceRate, ServiceRateRequest $request)
    {
        if ($productCategory->id === $serviceRate->productCategoryId) {
            $validatedRequest = $request->validated();
            $serviceRate->update($validatedRequest);
            if (isset($validatedRequest['default']) && $validatedRequest['default']) {
                $serviceRate->makeDefault();
            }
        } else {
            throw new HttpException(422, 'Insurance rate is not associated to the portal');
        }
    }

    public function editLeasingCondition(
        ProductCategory $productCategory,
        LeasingCondition $productCategoryLeasingCondition,
        LeasingConditionRequest $request
    ) {
        if ($productCategory->id === $productCategoryLeasingCondition->productCategoryId) {
            $productCategoryLeasingCondition->update($request->validated());
        } else {
            throw new HttpException(422, 'Insurance rate is not associated to the portal');
        }
    }

    public function deleteInsuranceRate(ProductCategory $productCategory, InsuranceRate $rate)
    {
        if ($productCategory->id === $rate->productCategoryId) {
            $rate->delete();
        } else {
            throw new HttpException(422, 'Insurance rate is not associated to the portal');
        }
    }

    public function deleteServiceRate(ProductCategory $productCategory, ServiceRate $rate)
    {
        if ($productCategory->id === $rate->productCategoryId) {
            $rate->delete();
        } else {
            throw new HttpException(422, 'Insurance rate is not associated to the portal');
        }
    }

    public function deleteLeasingCondition(ProductCategory $productCategory, LeasingCondition $leasingCondition)
    {
        if ($productCategory->id === $leasingCondition->productCategoryId) {
            $leasingCondition->delete();
        } else {
            throw new HttpException(422, 'Leasing condition is not associated to the portal');
        }
    }
}
