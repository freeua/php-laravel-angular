<?php

namespace App\Portal\Http\Controllers\V1\Supplier;

use App\Portal\Http\Controllers\Controller;
use App\Portal\Http\Resources\V1\CompanySimpleResource;
use Illuminate\Support\Facades\Auth;

/**
 * Class SupplierController
 *
 * @package App\Portal\Http\Controllers\V1\Supplier
 */
class SupplierController extends Controller
{
    /**
     * CompanyController constructor.
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Returns companies associated with supplier
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function companies()
    {
        return response()->success(CompanySimpleResource::collection(Auth::user()->supplier->companies));
    }
}
