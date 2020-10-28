<?php

namespace App\Portal\Services\Base;

use App\Helpers\StringHelper;
use App\Models\Permission;
use App\Portal\Models\Supplier;
use App\Portal\Notifications\ChangePassword;
use App\Portal\Helpers\AuthHelper;
use App\Portal\Notifications\Company\CompanyAdministratorCreate;
use App\Portal\Repositories\CompanyRepository;
use App\Portal\Repositories\PasswordHistoryRepository;
use App\Portal\Repositories\RoleRepository;
use App\Portal\Models\Role;
use App\Portal\Models\User;
use App\Portal\Notifications\UserCreated;
use App\Portal\Repositories\UserRepository;
use App\Portal\Services\WidgetService;
use App\Models\Portal;
use App\System\Repositories\PortalUserRepository;
use App\System\Repositories\SupplierRepository as SystemSupplierRepository;
use App\System\Services\SettingService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class UserService
 *
 * @package App\Portal\Services
 */
abstract class UserService
{
    /** @var SystemSupplierRepository */
    private $systemSupplierRepository;
    /** @var RoleRepository */
    protected $roleRepository;
    /** @var SettingService */
    protected $settingService;
    /** @var CompanyRepository */
    protected $companyRepository;
    /** @var WidgetService */
    protected $widgetService;
    /** @var PasswordHistoryRepository */
    protected $passwordHistoryRepository;
    /** @var PortalUserRepository */
    protected $portalUserRepository;
    /** @var UserRepository */
    protected $userRepository;

    /**
     * UserService constructor.
     *
     * @param UserRepository             $userRepository
     * @param PortalUserRepository $systemUserRepository
     * @param PasswordHistoryRepository  $passwordHistoryRepository
     * @param CompanyRepository          $companyRepository
     * @param WidgetService              $widgetService
     * @param SettingService             $settingService
     * @param RoleRepository             $roleRepository
     * @param SystemSupplierRepository   $systemSupplierRepository
     */
    public function __construct(
        UserRepository $userRepository,
        PortalUserRepository $portalUserRepository,
        PasswordHistoryRepository $passwordHistoryRepository,
        CompanyRepository $companyRepository,
        WidgetService $widgetService,
        SettingService $settingService,
        RoleRepository $roleRepository,
        SystemSupplierRepository $systemSupplierRepository
    ) {
        $this->userRepository = $userRepository;
        $this->portalUserRepository = $portalUserRepository;
        $this->passwordHistoryRepository = $passwordHistoryRepository;
        $this->companyRepository = $companyRepository;
        $this->widgetService = $widgetService;
        $this->settingService = $settingService;
        $this->roleRepository = $roleRepository;
        $this->systemSupplierRepository = $systemSupplierRepository;
    }

    /**
     * @param array  $data
     * @param string $role
     * @param Portal $portal
     * @param bool   $notify
     * @param bool   $first_company_admin
     *
     * @return User|false
     * @throws \Exception
     */
    public function create(array $data, string $role, Portal $portal, bool $notify = true, bool $first_company_admin = false)
    {
        $data['password'] = $data['password'] ?? StringHelper::password();

        $user = $this->userRepository->create($data, $portal);

        if ($user) {
            $user->guard_name = Role::GUARD_API;
            $user->assignRole($role);

            if ($notify) {
                if ($first_company_admin == true) {
                    $user->notify(new CompanyAdministratorCreate($portal, $data['password']));
                } else {
                    $user->notify(new UserCreated($data['password']));
                }
            }

            $this->passwordHistoryRepository->addNew($user->id, $user->password);

            $this->widgetService->addDefaultUserWidgets($user->id, $role);
        }

        return $user->fresh();
    }

    /**
     * @param array $data
     * @param Portal $portal
     * @return User|false
     * @throws \Exception
     */
    public function createAdmin(array $data, Portal $portal): User
    {
        return $this->create($data, Role::ROLE_PORTAL_ADMIN, $portal);
    }

    /**
     * @param array $data
     * @param Portal $portal
     * @param bool $first_company_admin
     * @return User|false
     * @throws \Exception
     */
    public function createCompanyAdmin(array $data, Portal $portal, bool $first_company_admin = false)
    {
        $data['code'] = $this->userRepository->generateCode($data['first_name'] . $data['last_name'], 3, 4);
        $user = $this->create($data, Role::ROLE_COMPANY_ADMIN, $portal, true, $first_company_admin);
        if ($user) {
            $user->guard_name = 'company';
            // giving all permissions to the new created company
            $permissions = Permission::query()->where('guard_name', '=', 'company')->get();
            foreach ($permissions as $permission) {
                $user->givePermissionTo($permission);
            }
        }
        return $user;
    }

    /**
     * @param array $data
     * @param Portal $portal
     * @return User|false
     * @throws \Exception
     */
    public function createSupplierAdmin(array $data, Portal $portal)
    {
        $data['code'] = $this->userRepository->generateCode($data['first_name'] . $data['last_name'], 3, 4);

        $created = $this->create($data, Role::ROLE_SUPPLIER_ADMIN, $portal);

        if (!$created) {
            return false;
        }

        return $created;
    }

    /**
     * @param array $data
     * @param Portal $portal
     * @return User|false
     * @throws \Exception
     */
    public function createEmployee(array $data, Portal $portal)
    {
        return $this->create($data, Role::ROLE_EMPLOYEE, $portal, false);
    }

    /**
     * Update user's profile
     *
     * @param array $data
     *
     * @return User|false
     * @throws \Exception
     */
    public function updateProfile(array $data)
    {
        /** @var User $user */
        $user = AuthHelper::user();

        $passwordReset = false;

        if (!empty($data['password'])) {
            $passwordReset = true;
            $data['password'] = Hash::make($data['password']);
            $data['password_updated_at'] = Carbon::now();
        }

        $result = $this->userRepository->update($user->id, $data);

        if (!$result) {
            return false;
        }

        if ($passwordReset) {
            $user->notify(new ChangePassword());

            $this->passwordHistoryRepository->addNew($user->id, $data['password']);
        }

        return $user->fresh();
    }

    /**
     * @param string $email
     *
     * @return bool
     */
    public function checkEmailAccess(string $email): bool
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user
            || !$user->isActive()
            || (!$user->portal->isActive())
            || ($user->isEmployee() && !$user->company->isActive())
        ) {
            return false;
        }

        return true;
    }

    /**
     * @param string $email
     *
     * @return string
     */
    public function getEmailAccessError(string $email): string
    {
        $user = $this->userRepository->findByEmail($email);

        if ($user && $user->isEmployee() && !$user->company->isActive()) {
            return __('auth.inactive_company');
        }

        return __('auth.invalid_email');
    }

    /**
     * @param string      $route
     * @param null|string $companySlug
     * @param string      $email
     *
     * @return bool
     */
    public function checkEmailAccessToModule(string $route, string $email, ?string $companySlug = null): bool
    {
        $user = $this->userRepository->findByEmail($email);

        return $user ? $this->checkUserAccessToModule($route, $companySlug, $user) : false;
    }

    /**
     * @param string      $route
     * @param null|string $companySlug
     * @param null|User   $user
     *
     * @return bool
     */
    public function checkUserAccessToModule(string $route, ?string $companySlug = null, ?User $user = null): bool
    {
        $user = $user ?: AuthHelper::user();
        $module = preg_split('/\//', $route)[0];
        $role = Role::getRouteRoleMap()[$module];
        if (!$user->hasRole($role)) {
            return false;
        }

        if ($user->isCompanyAdmin() || $user->isEmployee()) {
            if (!$companySlug) {
                throw new HttpException(422, __('auth.no_slug'));
            }
            if ($companySlug !== $user->company->slug) {
                return false;
            }
            if (!$user->company->isActive()) {
                throw new HttpException(403, __('auth.inactive'));
            }
        }

        if ($user->isSupplier()) {
            if (!$user->supplier->isActive()) {
                throw new HttpException(403, __('auth.inactive'));
            }
            $supplierPortalStatus = $user->supplier->portals()->find($user->portal_id)->pivot->status_id;
            if ($supplierPortalStatus != Supplier::STATUS_ACTIVE) {
                throw new HttpException(403, __('auth.inactive'));
            }
        }

        return true;
    }
}
