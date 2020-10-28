<?php

namespace App\Portal\Http\Controllers\V1;

use App\Portal\Http\Controllers\Controller;
use App\Portal\Http\Resources\V1\ProductColorResource;
use App\Portal\Http\Resources\V1\ProductSizeResource;
use App\Portal\Models\ProductColor;
use App\Portal\Models\ProductSize;

/**
 * Class ProductAttributeController
 *
 * @package App\Portal\Http\Controllers\V1
 */
class ProductAttributeController extends Controller
{
    public function getColors()
    {
        return response()->json(ProductColorResource::collection(ProductColor::all()));
    }

    public function getSizes()
    {
        return response()->json(ProductSizeResource::collection(ProductSize::all()));
    }
}
