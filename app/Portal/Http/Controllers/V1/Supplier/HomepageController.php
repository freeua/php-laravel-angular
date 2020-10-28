<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 28.03.2019
 * Time: 14:07
 */

namespace App\Portal\Http\Controllers\V1\Supplier;

use App\Portal\Helpers\AuthHelper;
use App\Portal\Http\Controllers\Controller;
use App\Portal\Http\Resources\V1\HomepageResource;
use App\Portal\Models\Homepage;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class HomepageController extends Controller
{
    public function get()
    {
        $supplier = AuthHelper::supplier();
        if (\Cache::has(Homepage::SUPPLIER_HOMEPAGE.'_'.$supplier->id)) {
            return response()->success(\Cache::get(Homepage::SUPPLIER_HOMEPAGE.'_'.$supplier->id));
        } else {
            if ($supplier->homepage) {
                $response = new HomepageResource($supplier->homepage);
                \Cache::put(Homepage::SUPPLIER_HOMEPAGE.'_'.$supplier->id, $response, 18000);
                return response()->success($response);
            } else {
                $response = Homepage::getDefaultHomepageByType(Homepage::SUPPLIER_DEFAULT_HOMEPAGE);
                \Cache::put(Homepage::SUPPLIER_HOMEPAGE.'_'.$supplier->id, $response, 18000);
                return response()->success($response);
            }
        }
    }
}
