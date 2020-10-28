<?php

namespace App\System\Services;

use App\Helpers\StringHelper;
use App\System\Notifications\ChangePassword;
use App\System\Models\User;
use App\System\Notifications\UserCreated;
use App\System\Repositories\PasswordHistoryRepository;
use App\System\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

/**
 * Class UserService
 *
 * @package App\System\Services
 */
class UserService
{
    /** @var WidgetService */
    private $widgetService;
    /** @var PasswordHistoryRepository */
    private $passwordHistoryRepository;
    /** @var SettingService */
    private $settingService;
    /** @var UserRepository */
    private $userRepository;

    /**
     * UserService constructor.
     *
     * @param UserRepository            $userRepository
     * @param SettingService            $settingService
     * @param PasswordHistoryRepository $passwordHistoryRepository
     * @param WidgetService             $widgetService
     */
    public function __construct(
        UserRepository $userRepository,
        SettingService $settingService,
        PasswordHistoryRepository $passwordHistoryRepository,
        WidgetService $widgetService
    ) {
        $this->userRepository = $userRepository;
        $this->settingService = $settingService;
        $this->passwordHistoryRepository = $passwordHistoryRepository;
        $this->widgetService = $widgetService;
    }

    /**
     * @param array $data
     *
     * @return User|false
     * @throws \Exception
     */
    public function create(array $data)
    {
        $data['password'] = StringHelper::password();

        $user = $this->userRepository->create($data);

        if ($user) {
            $user->notify(new UserCreated($data['password']));

            $this->widgetService->addDefaultUserWidgets($user->id);
            $this->passwordHistoryRepository->addNew($user->id, $user->password);
        }

        return $user;
    }

    /**
     * @param User  $user
     * @param array $data
     *
     * @return User
     */
    public function update(User $user, array $data): User
    {
        $this->userRepository->update($user->id, $data);

        return $user->fresh();
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
        $user = Auth::user();

        $passwordReset = false;

        if (!empty($data['password'])) {
            $passwordReset = true;
            $data['password'] = Hash::make($data['password']);
            $data['password_updated_at'] = Carbon::now();
        }

        $result = $this->userRepository->update($user->id, $data);

        if ($result && $passwordReset) {
            $user->notify(new ChangePassword());

            $this->passwordHistoryRepository->addNew($user->id, $data['password']);
        }

        return $result ? $user->fresh() : false;
    }
}
