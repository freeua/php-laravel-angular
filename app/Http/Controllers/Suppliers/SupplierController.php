<?php
namespace App\Http\Controllers\Suppliers;

use App\Http\Requests\Suppliers\SupplierListRequest;
use App\Portal\Helpers\AuthHelper;
use App\Portal\Http\Resources\V1\SupplierResource;
use App\Portal\Models\Supplier;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Routing\Controller;

class SupplierController extends Controller
{
    public function list(SupplierListRequest $request)
    {
        $query = Supplier::query();
        $user = AuthHelper::user();
        if ($user->isEmployee() || $user->isCompanyAdmin()) {
            $query->whereHas('companies', function (Builder $query) use ($user) {
                $query->where('company_id', $user->company_id);
            });
            if ($user->isEmployee()) {
                $query->where('status_id', '=', Supplier::STATUS_ACTIVE);
            }
        }
        return response()->json(SupplierResource::collection($query->get()));
    }
}
