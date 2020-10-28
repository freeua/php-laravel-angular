<?php

namespace App\Portal\Http\Controllers\V1;

use App\Helpers\PortalHelper;
use App\Http\Requests\DefaultListRequest;
use App\Http\Resources\PermissionResource;
use App\Models\Companies\Company;
use App\Models\Permission;
use App\Models\Portal;
use App\Portal\Http\Controllers\Controller;
use App\Portal\Http\Requests\V1\CompanySlugExistsRequest;
use App\Portal\Http\Requests\V1\CreateCompanyRequest;
use App\Portal\Http\Requests\V1\HomepageRequest;
use App\Portal\Http\Requests\V1\UpdateCompanyByAdminRequest;
use App\Portal\Http\Requests\V1\UpdateCompanyRequest;
use App\Portal\Http\Requests\V1\UploadPortalFilesRequest;
use App\Portal\Http\Resources\EmployeeResource;
use App\Portal\Http\Resources\V1\CompanyAdminResource;
use App\Portal\Http\Resources\V1\CompanyResource;
use App\Portal\Http\Resources\V1\CompanySimpleResource;
use App\Portal\Http\Resources\V1\HomepageResource;
use App\Portal\Http\Resources\V1\ListCollections\CompanyListCollection;
use App\Portal\Models\Homepage;
use App\Portal\Repositories\CompanyRepository;
use App\Portal\Services\CompanyService;
use App\Services\Companies\LeasingConditionService;
use App\Traits\UploadsFile;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EmployeeController extends Controller
{
    use UploadsFile;
    public function __construct()
    {
        parent::__construct();
    }

    public function getDefaultHomepage()
    {
        $default_for_portal = Homepage::getDefaultHomepageByType(Homepage::PORTAL_EMPLOYEE_DEFAULT_HOMEPAGE.'_'.PortalHelper::id());
        if (isset($default_for_portal->id)) {
            return response()->success($default_for_portal);
        } else {
            return response()->success(Homepage::getDefaultHomepageByType(Homepage::EMPLOYEE_DEFAULT_HOMEPAGE));
        }
    }
    public function updateDefaultHomepage(HomepageRequest $request)
    {
        $portal_id = PortalHelper::id();
        $data = $request->validated();
        \DB::beginTransaction();

        if (!empty($data['items']['logo'])) {
            if (!strpos($data['items']['logo'], '/logos/logo.png')) {
                $data['items']['logo'] = UploadsFile::handlePublicJsonFile($data['items']['logo'], "/homepages/default/portal/{$portal_id}/employee/logos", "logo.png");
            }
        } else {
            $data['items']['logo'] = null;
        }

        Homepage::updateOrCreate(['type'=>Homepage::PORTAL_EMPLOYEE_DEFAULT_HOMEPAGE.'_'.$portal_id], [
            'items' => $data['items']
        ]);
        \Cache::forget(Homepage::PORTAL_EMPLOYEE_DEFAULT_HOMEPAGE.'_'.$portal_id);
        \DB::commit();

        $homepage = Homepage::getDefaultHomepageByType(Homepage::PORTAL_EMPLOYEE_DEFAULT_HOMEPAGE.'_'.$portal_id);
        \Cache::put(Homepage::PORTAL_EMPLOYEE_DEFAULT_HOMEPAGE.'_'.$portal_id, $homepage, 18000);

        return $this->getDefaultHomepage();
    }
}
