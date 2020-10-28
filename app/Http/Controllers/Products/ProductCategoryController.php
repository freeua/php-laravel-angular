<?php

namespace App\Http\Controllers\Products;

use App\Http\Requests\InsuranceRateRequest;
use App\Http\Requests\Products\CreateProductCategoryRequest;
use App\Http\Requests\Products\EditProductCategoryRequest;
use App\Http\Requests\ServiceRateRequest;
use App\Http\Resources\LeasingSettings\LeasingConditionResource;
use App\Http\Resources\Products\ProductCategoryResource;
use App\Http\Resources\LeasingSettings\RateResource;
use App\Models\LeasingCondition;
use App\Models\ProductCategory;
use App\Models\Rates\InsuranceRate;
use App\Models\Rates\ServiceRate;
use App\Portal\Http\Requests\V1\LeasingConditionRequest;
use App\Services\Products\ProductCategoryService;
use Illuminate\Routing\Controller;

class ProductCategoryController extends Controller
{
    public $productCategoryService;
    public function __construct(ProductCategoryService $productCategoryService)
    {
        $this->productCategoryService = $productCategoryService;
    }

    public function list()
    {
        return response()->json(ProductCategoryResource::collection($this->productCategoryService->all()));
    }

    public function create(CreateProductCategoryRequest $request)
    {
        $productCategory = $this->productCategoryService->create($request->validated());
        return response()->json(ProductCategoryResource::make($productCategory));
    }

    public function edit(ProductCategory $productCategory, EditProductCategoryRequest $request)
    {
        $productCategory = $this->productCategoryService->edit($productCategory, $request->validated());
        return response()->json(ProductCategoryResource::make($productCategory));
    }

    public function delete(ProductCategory $productCategory)
    {
        $this->productCategoryService->delete($productCategory);
        return response()->json();
    }

    public function addInsuranceRate(ProductCategory $productCategory, InsuranceRateRequest $request)
    {
        return response()->json(new RateResource($this->productCategoryService->addInsuranceRate($productCategory, $request)));
    }

    public function addServiceRate(ProductCategory $productCategory, ServiceRateRequest $request)
    {
        return response()->json(new RateResource($this->productCategoryService->addServiceRate($productCategory, $request)));
    }

    public function addLeasingCondition(ProductCategory $productCategory, LeasingConditionRequest $request)
    {
        return response()->json(
            new LeasingConditionResource($this->productCategoryService->addLeasingCondition($productCategory, $request))
        );
    }

    public function editServiceRate(ProductCategory $productCategory, ServiceRate $serviceRate, ServiceRateRequest $request)
    {
        $this->productCategoryService->editServiceRate($productCategory, $serviceRate, $request);
    }

    public function editInsuranceRate(ProductCategory $productCategory, InsuranceRate $insuranceRate, InsuranceRateRequest $request)
    {
        $this->productCategoryService->editInsuranceRate($productCategory, $insuranceRate, $request);
    }

    public function editLeasingCondition(
        ProductCategory $productCategory,
        LeasingCondition $leasingCondition,
        LeasingConditionRequest $request
    ) {
        $this->productCategoryService->editLeasingCondition($productCategory, $leasingCondition, $request);
    }

    public function deleteInsuranceRate(ProductCategory $productCategory, InsuranceRate $insuranceRate)
    {
        $this->productCategoryService->deleteInsuranceRate($productCategory, $insuranceRate);
    }

    public function deleteServiceRate(ProductCategory $productCategory, ServiceRate $serviceRate)
    {
        $this->productCategoryService->deleteServiceRate($productCategory, $serviceRate);
    }

    public function deleteLeasingCondition(ProductCategory $productCategory, LeasingCondition $leasingCondition)
    {
        $this->productCategoryService->deleteLeasingCondition($productCategory, $leasingCondition);
    }
}
