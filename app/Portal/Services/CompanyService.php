<?php

namespace App\Portal\Services;

use App\Helpers\PortalHelper;
use App\Models\Companies\Company;
use App\Models\ProductCategory;
use App\Portal\Helpers\AuthHelper;
use App\Portal\Models\CompanyProductCategory;
use App\Portal\Models\Role;
use App\Portal\Models\User;
use App\Portal\Notifications\Company\CompanyChangedNotification;
use App\Portal\Notifications\Company\CompanyCreated;
use App\Portal\Notifications\Company\CompanyCreatedForPortalAdmin;
use App\Portal\Repositories\CompanyLeasingSettingRepository;
use App\Portal\Repositories\CompanyRepository;
use App\Portal\Repositories\UserRepository;
use App\Portal\Services\Company\SettingService;
use App\Traits\UploadsFile;
use Carbon\Carbon;
use Illuminate\Notifications\Notification;

/**
 * Class CompanyService
 *
 * @package App\Portal\Services
 */
class CompanyService
{
    use UploadsFile;

    /** @var UserService */
    private $userService;
    /** @var UserRepository */
    private $settingService;
    /** @var CompanyLeasingSettingRepository */
    protected $leasingSettingRepository;
    /** @var CompanyRepository */
    protected $companyRepository;

    public function __construct(
        CompanyRepository $companyRepository,
        CompanyLeasingSettingRepository $companyLeasingSettingRepository,
        SettingService $settingService,
        UserService $userService
    ) {
        $this->companyRepository = $companyRepository;
        $this->leasingSettingRepository = $companyLeasingSettingRepository;
        $this->settingService = $settingService;
        $this->userService = $userService;
    }

    public function create(array $data)
    {

        \DB::beginTransaction();
        $company = new Company();
        $data['leasingConditions'] = array_map(function ($leasingSetting) {
            $leasingSetting['activeAt'] = Carbon::parse($leasingSetting['activeAt']);
            if ($leasingSetting['inactiveAt']) {
                $leasingSetting['inactiveAt'] = Carbon::parse($leasingSetting['inactiveAt']);
            }
            return $leasingSetting;
        }, $data['leasingConditions']);
        $company->portal_id = PortalHelper::id();
        $company->status_id = $data['status']['id'];
        $logo = null;
        if (!empty($data['logo'])) {
            $logo = $data['logo'];
            unset($data['logo']);
        }
        $company->fill($data);
        if (!is_null($logo)) {
            $company->logo = UploadsFile::handlePublicJsonFile($logo, "/companies/{$company->id}/logos", "logo.png");
        }
        $company->saveOrFail();

        $admin = $this->createInitialAdmin($company);

        $company->suppliers()->attach(array_filter($data['supplier_ids']));

        if (isset($data['subcompany_ids'])) {
            foreach ($data['subcompany_ids'] as $id) {
                $subcompany = Company::find($id);
                $company->subcompanies()->save($subcompany);
            }
        }

        ProductCategory::all()->each(function (ProductCategory $productCategory) use ($company) {
            $status = true;
            if ($productCategory->name == 'S-Pedelec' && $company->s_pedelec_disable == true) {
                $status = false;
            }
            $companyProductCategory = new CompanyProductCategory([
                'company_id' => $company->id,
                'category_id' => $productCategory->id,
                'status' => $status
            ]);

            $companyProductCategory->saveOrFail();
        });

        \DB::commit();

        \Notification::send($admin, (new CompanyCreated($company, PortalHelper::getPortal())));

        $portalAdmins = app(UserRepository::class)->findByRole(Role::ROLE_PORTAL_ADMIN, $company->portal->id);
        
        \Notification::send($portalAdmins, new CompanyCreatedForPortalAdmin($company, PortalHelper::getPortal()));

        return $company->fresh();
    }

    public function update(Company $company, array $data)
    {
        if (!empty($data['logo'])) {
            $company->logo = UploadsFile::handlePublicJsonFile($data['logo'], "/companies/{$company->id}/logos", "logo.png");
        }
        unset($data['logo']);
        if ($company->is_accept_employee == false && $data['is_accept_employee'] == true) {
            User::where('portal_id', '=', PortalHelper::id())
                ->where('company_id', '=', $company->id)
                ->update(['is_accept_offer' => false]);
        }
        if (array_has($data, 'end_contract')) {
            $company->end_contract = new Carbon($data['end_contract']);
            unset($data['end_contract']);
        }
        $company->fill($data);
        $changedFields = $company->getDirty();
        $oldData = $company->getOriginal();
        $updated = $company->save();
        if (!$updated) {
            return false;
        }
        if ($company->uses_default_subsidies == true) {
            $company->users()->update([
                'individual_settings' => false
            ]);
        }
        if (isset($data['supplier_ids'])) {
            $changedSuppliers =
                $this->handleSuppliers($company, $data['supplier_ids']);
            if (count(array_diff($changedSuppliers['old'], $changedSuppliers['new'])) > 0) {
                $oldData['suppliers'] = $changedSuppliers['old'];
                $changedFields['suppliers'] = $changedSuppliers['new'];
            }
        }

        if (isset($data['subcompany_ids'])) {
            $changedSubcompanies =
                $this->handleSubcompanies($company, $data['subcompany_ids']);
            if ($changedSubcompanies['total'] > 0 && count(array_diff($changedSubcompanies['old'], $changedSubcompanies['new'])) > 0) {
                $oldData['subcompanies'] = $changedSubcompanies['old'];
                $changedFields['subcompanies'] = $changedSubcompanies['new'];
            }
        }

        $productCategory = ProductCategory::where('name', 'S-Pedelec')->first();
        if ($productCategory != false) {
            CompanyProductCategory::where('company_id', $company->id)->where('category_id', $productCategory->id)->update([
                'status' => !$company->s_pedelec_disable
            ]);
        }

        if (count($changedFields) > 0) {
            $this->notifyAdmins(new CompanyChangedNotification($company, $oldData, AuthHelper::user(), $changedFields));
        }

        return $company->fresh();
    }

    public function changeLogo(Company $company, string $logo)
    {
        $company->logo = $logo;
        $company->save();
    }

    public function notifyAdmins(Notification $notification)
    {
        $systemAdmins = \App\System\Models\User::query()->get();
        \Notification::send($systemAdmins, $notification);
    }

    public function handleSuppliers(Company $company, array $ids): array
    {
        $exitsSuppliersIds = $company->suppliers->pluck('id', 'id');
        $currentSuppliersIds = collect($ids);

        $this->companyRepository->detachSuppliers($company, $exitsSuppliersIds->diff($currentSuppliersIds)->toArray());
        $company->suppliers()->attach($currentSuppliersIds->diff($exitsSuppliersIds));
        return [
            'old' => $exitsSuppliersIds->toArray(),
            'new' => $company->suppliers()->get()->pluck('id', 'id')->toArray(),
        ];
    }

    public function handleSubcompanies(Company $company, array $ids): array
    {
        $exitsSubcompaniesIds = $company->subcompanies->pluck('id', 'id');
        $currentSubcompaniesIds = collect($ids);

        $existingSubcompanies = Company::where('parent_id', $company->id);
        $existingSubcompanies->update(['parent_id' => null]);

        foreach ($currentSubcompaniesIds as $id) {
            $subcompany = Company::find($id);
            $company->subcompanies()->save($subcompany);
        }

        return [
            'total' => Company::where('parent_id', $company->id)->get()->count(),
            'old' => $exitsSubcompaniesIds->toArray(),
            'new' => $company->subcompanies()->get()->pluck('id', 'id')->toArray(),
        ];
    }

    /**
     * @param Company $company
     *
     * @return User|false
     * @throws \Exception
     */
    private function createInitialAdmin(Company $company)
    {
        $data['user'] = [
            'company_id' => $company->id,
            'first_name' => $company->admin_first_name,
            'last_name' => $company->admin_last_name,
            'email' => $company->admin_email
        ];

        return $this->userService->createCompanyAdmin($data['user'], PortalHelper::getPortal(), true);
    }

    public function listEmployees(Company$company)
    {
        $employees = $company->employees()->get();
        return $employees;
    }

    public function listAdmins(Company $company)
    {
        $companyAdmins = $company->employeeAdmins()->with('permissions')->get();
        return $companyAdmins;
    }

    public function listGroups(Company $company)
    {
        $subcompanies = $company->subcompanies()->get();
        $parentCompanies = $company->parent()->get();

        $result = collect();

        foreach ($subcompanies as $subcompany) {
            $result->push($subcompany);
        }

        foreach ($parentCompanies as $parentCompany) {
            $result->push($parentCompany);
        }

        return $result;
    }
}
