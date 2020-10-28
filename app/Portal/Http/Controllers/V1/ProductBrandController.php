<?php

namespace App\Portal\Http\Controllers\V1;

use App\Portal\Http\Controllers\Controller;
use App\Portal\Http\Resources\V1\ProductBrandResource;
use App\Portal\Repositories\ProductBrandRepository;

/**
 * Class ProductBrandController
 *
 * @package App\Portal\Http\Controllers\V1
 */
class ProductBrandController extends Controller
{
    /** @var ProductBrandRepository */
    private $productBrandRepository;

    /**
     * Create a new controller instance.
     *
     * @param ProductBrandRepository $productBrandRepository
     */
    public function __construct(ProductBrandRepository $productBrandRepository)
    {
        parent::__construct();

        $this->productBrandRepository = $productBrandRepository;
    }

    /**
     * Returns all product brands
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function all()
    {
        $productBrands = $this->productBrandRepository->allFiltered();

        return response()->json(ProductBrandResource::collection($productBrands));
    }
}
