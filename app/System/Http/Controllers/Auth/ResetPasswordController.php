<?php

namespace App\System\Http\Controllers\Auth;

use App\Rules\NewPassword;
use App\Rules\StrongPassword;
use App\System\Http\Controllers\Controller;
use App\System\Models\User;
use App\System\Notifications\ChangePassword;
use App\System\Repositories\PasswordHistoryRepository;
use App\Services\Emails\EmailService;
use Carbon\Carbon;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Validation\ValidationException;
use Validator;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Contracts\Hashing\Hasher;

/**
 * Class ResetPasswordController
 *
 * @package App\System\Http\Controllers\Auth
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
    /** @var PasswordHistoryRepository */
    private $passwordHistoryRepository;
    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    protected $hasher;

    public function __construct(
        PasswordHistoryRepository $passwordHistoryRepository,
        Hasher $hasher
    ) {
        parent::__construct();

        $this->passwordHistoryRepository = $passwordHistoryRepository;
        $this->hasher = $hasher;
    }

    protected function sendResetResponse(Request $request, $response)
    {
        return response()->success([], __($response));
    }


    protected function sendResetFailedResponse(Request $request, $response)
    {
        return response()->error(
            ['email' => __($response)],
            __($response)
        );
    }

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

    protected function rules()
    {
        return [
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => ['required', 'confirmed', new StrongPassword()],
        ];
    }

    public function broker()
    {
        return Password::broker('system-users');
    }

    protected function guard()
    {
        return Auth::guard(config('auth.system_guard'));
    }

    public function userInfo()
    {
        $passwordReset = \DB::table('password_resets')
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
