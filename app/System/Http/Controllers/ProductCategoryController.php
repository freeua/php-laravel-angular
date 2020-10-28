<?php

namespace App\System\Http\Controllers;

use App\Http\Requests\Products\CreateProductCategoryRequest;
use App\Http\Resources\Products\ProductCategoryResource;
use App\Services\Products\ProductCategoryService;

/**
 * Class ProductCategoryController
 *
 * @package App\System\Http\Controllers
 */
class ProductCategoryController extends Controller
{

    /** @var ProductCategoryService */
    private $productCategoryService;

    public function __construct(ProductCategoryService $productCategoryService)
    {
        parent::__construct();

        $this->productCategoryService = $productCategoryService;
    }

    public function all()
    {
        $productCategories = $this->productCategoryService->all();

        return response()->success(ProductCategoryResource::collection($productCategories));
    }

    public function create(CreateProductCategoryRequest $request)
    {
        $createdCategory = $this->productCategoryService->create($request->validated());
        return response(new ProductCategoryResource($createdCategory));
    }
}
