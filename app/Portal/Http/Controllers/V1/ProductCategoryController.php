<?php

namespace App\Portal\Http\Controllers\V1;

use App\Models\Companies\Company;
use App\Portal\Helpers\AuthHelper;
use App\Portal\Http\Controllers\Controller;
use App\Portal\Http\Resources\V1\ProductCategoryResource;
use App\Portal\Repositories\ProductCategoryRepository;

/**
 * Class ProductCategoryController
 *
 * @package App\Portal\Http\Controllers\V1
 */
class ProductCategoryController extends Controller
{
    /** @var ProductCategoryRepository */
    private $productCategoryRepository;

    /**
     * Create a new controller instance.
     *
     * @param ProductCategoryRepository $productCategoryRepository
     */
    public function __construct(ProductCategoryRepository $productCategoryRepository)
    {
        parent::__construct();

        $this->productCategoryRepository = $productCategoryRepository;
    }

    /**
     * Returns all product categories
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function all()
    {
        $productCategories = $this->productCategoryRepository->all();

        return response()->json(ProductCategoryResource::collection($productCategories));
    }

    public function allCompany()
    {
        $productCategories = $this->productCategoryRepository->allCompany(AuthHelper::company());

        return response()->json(ProductCategoryResource::collection($productCategories));
    }

    public function allForSupplier(Company $company)
    {
        $productCategories = $this->productCategoryRepository->allCompany($company);

        return response()->json(ProductCategoryResource::collection($productCategories));
    }
}
