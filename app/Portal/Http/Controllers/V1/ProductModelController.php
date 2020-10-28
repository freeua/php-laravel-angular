<?php

namespace App\Portal\Http\Controllers\V1;

use App\Portal\Http\Controllers\Controller;
use App\Portal\Http\Requests\V1\ProductModelsRequest;
use App\Portal\Http\Resources\V1\ProductModelResource;
use App\Portal\Repositories\ProductModelRepository;

/**
 * Class ProductModelController
 *
 * @package App\Portal\Http\Controllers\V1
 */
class ProductModelController extends Controller
{
    /** @var ProductModelRepository */
    private $productModelRepository;

    /**
     * Create a new controller instance.
     *
     * @param ProductModelRepository $productModelRepository
     */
    public function __construct(ProductModelRepository $productModelRepository)
    {
        parent::__construct();

        $this->productModelRepository = $productModelRepository;
    }

    /**
     * Returns all product sizes
     *
     * @param ProductModelsRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function all(ProductModelsRequest $request)
    {
        $productSizes = $this->productModelRepository->allFiltered($request->validated());

        return response()->json(ProductModelResource::collection($productSizes));
    }
}
