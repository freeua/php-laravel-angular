<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 20.03.2019
 * Time: 13:08
 */

namespace App\Portal\Http\Controllers\V1\Employee;

use App\Helpers\PortalHelper;
use App\Portal\Helpers\AuthHelper;
use App\Portal\Http\Controllers\Controller;
use App\Portal\Http\Resources\V1\HomepageResource;
use App\Portal\Models\Homepage;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class HomepageController extends Controller
{
    public function get()
    {
        $company = AuthHelper::company();
        if (\Cache::has(Homepage::EMPLOYEE_HOMEPAGE.'_'.$company->id)) {
            return response()->success(\Cache::get(Homepage::EMPLOYEE_HOMEPAGE.'_'.$company->id));
        } else {
            if ($company->getEmployeeHomePage()) {
                $response = new HomepageResource($company->getEmployeeHomePage());
                \Cache::put(Homepage::EMPLOYEE_HOMEPAGE.'_'.$company->id, $response, 18000);
                return response()->success($response);
            }

            if (\Cache::has(Homepage::PORTAL_EMPLOYEE_DEFAULT_HOMEPAGE.'_'.$company->portal->id)) {
                return response()->success(\Cache::get(Homepage::PORTAL_EMPLOYEE_DEFAULT_HOMEPAGE.'_'.$company->portal->id));
            }

            $response = Homepage::getDefaultHomepageByType(Homepage::PORTAL_EMPLOYEE_DEFAULT_HOMEPAGE . '_' . $company->portal->id);
            if (isset($response->id)) {
                \Cache::put(Homepage::PORTAL_EMPLOYEE_DEFAULT_HOMEPAGE . '_' . $company->portal->id, $response, 18000);
                return response()->success($response);
            }

            if (\Cache::has(Homepage::EMPLOYEE_DEFAULT_HOMEPAGE)) {
                return response()->success(\Cache::get(Homepage::EMPLOYEE_DEFAULT_HOMEPAGE));
            }

            $response = Homepage::getDefaultHomepageByType(Homepage::EMPLOYEE_DEFAULT_HOMEPAGE);
            \Cache::put(Homepage::EMPLOYEE_DEFAULT_HOMEPAGE, $response, 18000);
            return response()->success($response);
        }
    }
}
