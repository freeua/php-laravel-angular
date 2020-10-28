<?php

namespace App\Portal\Http\Controllers\Auth;

use App\Portal\Helpers\AuthHelper;
use App\Portal\Http\Controllers\Controller;
use App\Portal\Models\User;
use App\Portal\Notifications\ChangePassword;
use App\Portal\Repositories\PasswordHistoryRepository;
use App\Portal\Services\UserService;
use App\Rules\NewPassword;
use App\Rules\StrongPassword;
use Carbon\Carbon;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Validator;

/**
 * Class ResetPasswordController
 *
 * @package App\Portal\Http\Controllers\Auth
 */
class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;
    /** @var UserService */
    private $userService;
    /** @var PasswordHistoryRepository */
    private $passwordHistoryRepository;
    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    protected $hasher;

    /**
     * Create a new controller instance.
     *
     * @param PasswordHistoryRepository $passwordHistoryRepository
     * @param UserService $userService
     */
    public function __construct(
        PasswordHistoryRepository $passwordHistoryRepository,
        UserService $userService,
        Hasher $hasher
    ) {
        parent::__construct();

        $this->passwordHistoryRepository = $passwordHistoryRepository;
        $this->hasher = $hasher;
        $this->userService = $userService;
    }

    /**
     * Get the response for a successful password reset.
     *
     * @param  string $response
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetResponse(Request $request, $response)
    {
        $user = AuthHelper::user();

        return response()->success(
            [
                'redirect_to' => $user->getLoginPath()
            ],
            __($response)
        );
    }

    /**
     * Get the response for a failed password reset.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  string $response
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetFailedResponse(Request $request, $response)
    {
        return response()->error(
            ['email' => __($response)],
            __($response)
        );
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword $user
     * @param  string $password
     *
     * @return void
     * @throws ValidationException
     * @throws \Exception
     */
    protected function resetPassword($user, $password)
    {
        /** @var User $user */
        $validator = Validator::make(['password' => $password], [
            'password' => ['not_contains:' . $user->first_name . ',' . $user->last_name, new NewPassword($user->id)],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $user->password = Hash::make($password);
        $user->password_updated_at = Carbon::now();

        $user->save();

        $this->passwordHistoryRepository->addNew($user->id, $user->password);

        event(new PasswordReset($user));

        $user->notify(new ChangePassword());

        $this->guard()->login($user);
    }

    /**
     * Get the password reset validation rules.
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', new StrongPassword()],
        ];
    }

    /**
     * @inheritdoc
     */
    public function reset(Request $request)
    {
        $this->validate($request, $this->rules(), $this->validationErrorMessages());

        if (!$this->userService->checkEmailAccess($request->get('email'))) {
            $error = ValidationException::withMessages(['email' => $this->userService->getEmailAccessError($request->get('email'))]);
            throw $error;
        }

        if (!$this->userService->checkEmailAccessToModule($request->route()->getPrefix(), $request->get('email'), $request->companySlug())) {
            $error = ValidationException::withMessages(['email' => __('auth.not_exists_on_module')]);
            throw $error;
        }

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $response = $this->broker()->reset(
            $this->credentials($request),
            function ($user, $password) {
                $this->resetPassword($user, $password);
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        return $response == Password::PASSWORD_RESET
            ? $this->sendResetResponse($request, $response)
            : $this->sendResetFailedResponse($request, $response);
    }

    public function userInfo()
    {
        $passwordReset = \DB::table('portal_password_resets')
            ->where('email', '=', request('email'))
            ->first();

        if ($passwordReset) {
            if ($this->hasher->check(request('token'), $passwordReset->token)) {
                $user = User::query()->where('email', request('email'))->first();
                return response()->success([
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                ]);
            } else {
                return response()->error(
                    [__('auth.not_exists_on_module')],
                    __('auth.not_exists_on_module'),
                    422
                );
            }
        } else {
            return response()->error(
                [__('auth.incorrect_token')],
                __('auth.incorrect_token'),
                422
            );
        }
    }
}
