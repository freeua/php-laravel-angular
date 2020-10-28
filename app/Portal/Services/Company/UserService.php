<?php

namespace App\Portal\Services\Company;

use App\Helpers\PortalHelper;
use App\Portal\Helpers\AuthHelper;
use App\Portal\Models\User;
use App\Portal\Notifications\Registration\RegistrationApproved;
use App\Portal\Notifications\Registration\RegistrationCompleted;
use App\Portal\Notifications\Registration\RegistrationLink;
use App\Portal\Notifications\Registration\RegistrationNew;
use App\Portal\Notifications\Registration\RegistrationRejected;
use App\Portal\Models\Role;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

/**
 * Class UserService
 *
 * @package App\Portal\Services\Company
 */
class UserService extends \App\Portal\Services\Base\UserService
{
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
     * @param $id
     *
     * @return bool
     */
    public function approve($id): bool
    {
        \DB::beginTransaction();
        $result = $this->userRepository->update($id, ['status_id' => User::STATUS_ACTIVE]);

        if (!$result) {
            return false;
        }

        $user = $this->userRepository->find($id);

        $user->notify(new RegistrationApproved($user->portal));
        \DB::commit();
        return true;
    }

    /**
     * @param $id
     *
     * @return bool
     */
    public function reject($id): bool
    {
        $result = $this->userRepository->update($id, ['status_id' => User::STATUS_INACTIVE, 'rejected_by' => AuthHelper::id()]);

        if (!$result) {
            return false;
        }
        $user = $this->userRepository->find($id);

        $user->notify(new RegistrationRejected());

        return true;
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function canBeApprovedRejected(User $user): bool
    {
        return in_array(Role::ROLE_EMPLOYEE, $user->getRoleNames()->toArray()) && $user->status_id === User::STATUS_PENDING;
    }

    /**
     * @param string $email
     * @param string $companySlug
     *
     * @return void
     */
    public function sendRegistrationLink(string $email, string $companySlug)
    {
        $url = URL::signedRoute('registration.register', ['id' => str_random(32), 'email' => $email]);

        $path = str_replace('{companySlug}', $companySlug, Role::getRoleModulePath(Role::ROLE_EMPLOYEE));
        $url = str_replace(str_before($url, '/registration/register'), PortalHelper::frontendUrl() . $path, $url);

        Notification::route('mail', $email)->notify(new RegistrationLink($url));
    }

    /**
     * @param array $data
     *
     * @return bool
     * @throws \Exception
     */
    public function register(array $data): bool
    {
        $company = $this->companyRepository->findBySlug($data['company_slug'], PortalHelper::id());

        if (!$company) {
            return false;
        }

        $data['company_id'] = $company->id;
        $data['status_id'] = User::STATUS_PENDING;

        $user = $this->createEmployee($data, PortalHelper::getPortal());

        if (!$user) {
            return false;
        }

        $user->notify(new RegistrationCompleted(PortalHelper::getPortal()));

        $companyAdmins = $this->userRepository->findByRole(Role::ROLE_COMPANY_ADMIN, PortalHelper::getPortal()->id, null, $company->id);

        if ($companyAdmins) {
            Notification::send($companyAdmins, (new RegistrationNew($user)));
        }

        return true;
    }
}
