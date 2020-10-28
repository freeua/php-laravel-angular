<?php

namespace App\Portal\Services;

use App\Helpers\PortalHelper;
use App\Portal\Helpers\AuthHelper;
use App\Models\Audit;
use App\Models\Permission;
use App\Models\Companies\Company;
use App\Portal\Models\Role;
use App\Portal\Models\User;
use App\Portal\Notifications\Company\CompanyAdministratorChanged;
use App\Portal\Notifications\UserCreated;
use Carbon\Carbon;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

/**
 * Class UserService
 *
 * @package App\Portal\Services
 */
class UserService extends Base\UserService
{
    /**
     * @param array $data
     *
     * @return User|false
     * @throws \Exception
     * @throws \Exception
     */
    public function selfCreate(array $data)
    {
        if ($data['role'] === Role::ROLE_PORTAL_ADMIN) {
            \DB::beginTransaction();
            $user = $this->createAdmin($data, PortalHelper::getPortal());
            if (isset($data['hasEditPermission']) && $data['hasEditPermission']) {
                $user->guard_name = 'portal';
                $user->givePermissionTo([Permission::EDIT_PORTAL_DATA]);
            }
            \DB::commit();
            return $user;
        } else {
            return $this->createCompanyAdmin($data, PortalHelper::getPortal());
        }
    }

    /**
     * @param User  $user
     * @param array $data
     *
     * @return User|false
     */
    public function selfUpdate(User $user, array $data)
    {
        $updated = $this->userRepository->update($user->id, $data);

        if (!$updated) {
            return false;
        }

        return $user->fresh();
    }

    /**
     * @param User $user
     * @param array $data
     * @return User|bool|null
     * @throws \Exception
     */
    public function selfUpdateDetails(User $user, array $data)
    {
        if (!empty($data['password'])) {
            $password = $data['password'];
            $data['password'] = Hash::make($password);
            $data['password_updated_at'] = Carbon::now();
        }
        \DB::beginTransaction();
        $result = $this->userRepository->update($user->id, $data);
        if ($user->isAdmin()) {
            $user->guard_name = 'portal';
            if ($data['hasEditPermission']) {
                $user->givePermissionTo([Permission::EDIT_PORTAL_DATA]);
            } else {
                $user->revokePermissionTo(Permission::EDIT_PORTAL_DATA);
            }
        }
        \DB::commit();

        if (!$result) {
            return false;
        }

        if (isset($password)) {
            $user->notify(new UserCreated($password));
        }

        return $user->fresh();
    }



    public function updatePermissions(User $user, array $permissions)
    {
        $user->guard_name = 'company';
        $user->syncPermissions(array_pluck($permissions, 'id'));
        $user->guard_name = 'api';
        if (count($permissions) === 0) {
            $user->removeRole(Role::ROLE_COMPANY_ADMIN);
            $user->assignRole(Role::ROLE_EMPLOYEE);
        } else {
            $user->assignRole(Role::ROLE_COMPANY_ADMIN);
        }
        $this->notifyAdmins(new CompanyAdministratorChanged($user->company, \Auth::user(), $user));
    }

    public function updateCompany(User $user, array $companyData)
    {
        $oldUser = clone $user;
        $user->company_id = $companyData['company']['id'];
        $user->save();
        Audit::userCompanySwitch($oldUser, $user, AuthHelper::user());
        return $user;
    }

    public function listPermissions(string $guard) : Collection
    {
        return Permission::query()->where('guard_name', '=', $guard)->get();
    }

    public function notifyAdmins(Notification $notification)
    {
        $systemAdmins = \App\System\Models\User::query()->get();
        \Notification::send($systemAdmins, $notification);
    }
}
