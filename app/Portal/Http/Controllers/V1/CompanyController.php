<?php

namespace App\Portal\Http\Controllers\V1;

use App\Helpers\PortalHelper;
use App\Http\Requests\DefaultListRequest;
use App\Http\Resources\PermissionResource;
use App\Http\Resources\GroupResource;
use App\Portal\Http\Resources\V1\Company\CompanyUserResource;
use App\Models\Companies\Company;
use App\Models\Permission;
use App\Models\Portal;
use App\Portal\Models\User;
use App\Portal\Http\Controllers\Controller;
use App\Portal\Http\Requests\V1\CompanySlugExistsRequest;
use App\Portal\Http\Requests\V1\CreateCompanyRequest;
use App\Portal\Http\Requests\V1\HomepageRequest;
use App\Portal\Http\Requests\V1\UpdateCompanyByAdminRequest;
use App\Portal\Http\Requests\V1\UpdateCompanyRequest;
use App\Portal\Http\Requests\V1\UpdateUserCompanyRequest;
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
use App\Portal\Services\UserService;
use App\Services\Companies\LeasingConditionService;
use App\Traits\UploadsFile;

class CompanyController extends Controller
{
    use UploadsFile;
    /** @var CompanyService */
    private $companyService;
    /** @var CompanyRepository */
    private $companyRepository;
    private $leasingConditionService;
    /** @var UserService */
    private $userService;

    public function __construct(
        CompanyRepository $companyRepository,
        CompanyService $companyService,
        LeasingConditionService $leasingConditionService,
        UserService $userService
    ) {
        parent::__construct();
        $this->leasingConditionService = $leasingConditionService;
        $this->companyRepository = $companyRepository;
        $this->companyService = $companyService;
        $this->userService = $userService;
    }

    public function index(DefaultListRequest $request)
    {
        $companies = $this->companyRepository->list($request->validated(), ['orders', 'orders.contract']);

        return response()->success(new CompanyListCollection($companies));
    }

    public function all()
    {
        $companies = Company::query()->where('portal_id', PortalHelper::id())->get();

        return response()->success(CompanySimpleResource::collection($companies));
    }

    public function create(CreateCompanyRequest $request)
    {
        $company = $this->companyService->create($request->validated());

        return response()->json(new CompanyResource($company->load('leasingConditions')));
    }

    public function view(Company $company)
    {
        return response()->json(new CompanyResource($company->load('leasingConditions')));
    }

    public function update(Company $company, UpdateCompanyRequest $request)
    {
        $company = $this->companyService->update($company, $request->validated());
        return response()->json(new CompanyResource($company->load('leasingConditions')));
    }

    public function updateByCompanyAdmin(Company $company, UpdateCompanyByAdminRequest $request)
    {
        $company = $this->companyService->update($company, $request->validated());
        return response()->json(new CompanyResource($company->load('leasingConditions')));
    }

    public function listEmployees(Company $company)
    {
        $employees = $this->companyService->listEmployees($company);
        return response()->json(EmployeeResource::collection($employees));
    }

    public function listAdmins(Company $company)
    {
        $employees = $this->companyService->listAdmins($company);
        return response()->success(CompanyAdminResource::collection($employees));
    }

    public function listPermissions()
    {
        return response()->json(
            PermissionResource::collection(Permission::query()->where('guard_name', 'company')->get())
        );
    }

    public function listGroups(Company $company)
    {
        $groups = $this->companyService->listGroups($company);
        return response()->json(GroupResource::collection($groups));
    }

    public function switchGroup(User $user, UpdateUserCompanyRequest $request)
    {
        $user = $this->userService->updateCompany($user, $request->validated());
        return response()->json(new CompanyUserResource($user->load(['audits'])));
    }

    public function slugExists(CompanySlugExistsRequest $request)
    {
        $exists = $this->companyRepository->findBySlug($request->input('slug'), PortalHelper::id());

        return response()->success(['result' => $exists]);
    }

    public function getHomepage(Company $company)
    {
        if ($company->getCompanyHomePage()) {
            return response()->success(new HomepageResource($company->getCompanyHomePage()));
        } else {
            $default_for_portal = Homepage::getDefaultHomepageByType(Homepage::PORTAL_COMPANY_DEFAULT_HOMEPAGE.'_'.PortalHelper::id());
            if (isset($default_for_portal->id)) {
                return response()->success($default_for_portal);
            } else {
                return response()->success(Homepage::getDefaultHomepageByType(Homepage::COMPANY_DEFAULT_HOMEPAGE));
            }
        }
    }

    public function updateHomepage(Company $company, HomepageRequest $request)
    {
        $data = $request->validated();
        \DB::beginTransaction();

        if (!empty($data['items']['logo'])) {
            if (!strpos($data['items']['logo'], '/logos/logo.png')) {
                $data['items']['logo'] = UploadsFile::handlePublicJsonFile($data['items']['logo'], "/homepages/company/{$company->id}/logos", "logo.png");
            }
        } else {
            unset($data['items']['logo']);
        }
        $company->homepage()->updateOrCreate(['homepageable_id'=>$company->id, 'homepageable_type'=>Company::ENTITY], [
            'items' => $data['items'],
            'type' => Homepage::COMPANY_HOMEPAGE
        ]);
        \Cache::forget(Homepage::COMPANY_HOMEPAGE.'_'.$company->id);
        \DB::commit();

        return response()->success(new HomepageResource($company->getCompanyHomePage()));
    }

    public function getDefaultHomepage()
    {
        $default_for_portal = Homepage::getDefaultHomepageByType(Homepage::PORTAL_COMPANY_DEFAULT_HOMEPAGE.'_'.PortalHelper::id());
        if (isset($default_for_portal->id)) {
            return response()->success($default_for_portal);
        } else {
            return response()->success(Homepage::getDefaultHomepageByType(Homepage::COMPANY_DEFAULT_HOMEPAGE));
        }
    }
    public function updateDefaultHomepage(HomepageRequest $request)
    {
        $portal_id = PortalHelper::id();
        $data = $request->validated();
        \DB::beginTransaction();

        if (!empty($data['items']['logo'])) {
            if (!strpos($data['items']['logo'], '/logos/logo.png')) {
                $data['items']['logo'] = UploadsFile::handlePublicJsonFile($data['items']['logo'], "/homepages/default/portal/{$portal_id}/company/logos", "logo.png");
            }
        } else {
            $data['items']['logo'] = null;
        }

        Homepage::updateOrCreate(['type'=>Homepage::PORTAL_COMPANY_DEFAULT_HOMEPAGE.'_'.$portal_id], [
            'items' => $data['items']
        ]);
        \Cache::forget(Homepage::PORTAL_COMPANY_DEFAULT_HOMEPAGE.'_'.$portal_id);
        \DB::commit();

        $homepage = Homepage::getDefaultHomepageByType(Homepage::PORTAL_COMPANY_DEFAULT_HOMEPAGE.'_'.$portal_id);
        \Cache::put(Homepage::PORTAL_COMPANY_DEFAULT_HOMEPAGE.'_'.$portal_id, $homepage, 18000);

        return $this->getDefaultHomepage();
    }
}
