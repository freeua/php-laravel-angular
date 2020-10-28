<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 20.03.2019
 * Time: 13:00
 */

namespace App\Portal\Http\Controllers\V1\Company;

use App\Helpers\PortalHelper;
use App\Portal\Helpers\AuthHelper;
use App\Portal\Http\Controllers\Controller;
use App\Portal\Http\Requests\V1\HomepageRequest;
use App\Portal\Http\Resources\V1\HomepageResource;
use App\Portal\Models\Homepage;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class HomepageController extends Controller
{
    public function get()
    {
        $company = AuthHelper::company();
        if (\Cache::has(Homepage::COMPANY_HOMEPAGE.'_'.$company->id)) {
            return response()->success(\Cache::get(Homepage::COMPANY_HOMEPAGE.'_'.$company->id));
        } else {
            if ($company->getCompanyHomePage()) {
                $response = new HomepageResource($company->getCompanyHomePage());
                \Cache::put(Homepage::COMPANY_HOMEPAGE.'_'.$company->id, $response, 18000);
                return response()->success($response);
            }

            if (\Cache::has(Homepage::PORTAL_COMPANY_DEFAULT_HOMEPAGE.'_'.$company->portal->id)) {
                return response()->success(\Cache::get(Homepage::PORTAL_COMPANY_DEFAULT_HOMEPAGE.'_'.$company->portal->id));
            }

            $response = Homepage::getDefaultHomepageByType(Homepage::PORTAL_COMPANY_DEFAULT_HOMEPAGE . '_' . $company->portal->id);
            if (isset($response->id)) {
                \Cache::put(Homepage::PORTAL_COMPANY_DEFAULT_HOMEPAGE . '_' . $company->portal->id, $response, 18000);
                return response()->success($response);
            }

            if (\Cache::has(Homepage::COMPANY_DEFAULT_HOMEPAGE)) {
                return response()->success(\Cache::get(Homepage::COMPANY_DEFAULT_HOMEPAGE));
            }

            $response = Homepage::getDefaultHomepageByType(Homepage::COMPANY_DEFAULT_HOMEPAGE);
            \Cache::put(Homepage::COMPANY_DEFAULT_HOMEPAGE, $response, 18000);
            return response()->success($response);
        }
    }
}
