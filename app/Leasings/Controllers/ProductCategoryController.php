<?php
namespace App\Leasings\Controllers;

use Illuminate\Routing\Controller;
use App\Models\ProductCategory;
use App\Leasings\Resources\ProductCategoryResource;

class ProductCategoryController extends Controller
{
    public function list()
    {
        return response()->json(ProductCategoryResource::collection(
            ProductCategory::all()
        ));
    }
}
