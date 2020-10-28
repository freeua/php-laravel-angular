<?php

namespace App\Portal\Http\Controllers\Auth;

use App\Portal\Http\Controllers\Controller;
use App\Portal\Services\UserService;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * Class ForgotPasswordController
 *
 * @package App\Portal\Http\Controllers\Auth
 */
class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;
    /** @var UserService */
    private $userService;

    /**
     * Create a new controller instance.
     *
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        parent::__construct();

        $this->userService = $userService;
    }

    /**
     * Get the response for a successful password reset link.
     *
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetLinkResponse(Request $request, $response)
    {
        return response()->success(
            [],
            __($response)
        );
    }

    /**
     * Get the response for a failed password reset link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        return response()->error(
            ['email' => __($response)],
            __($response)
        );
    }

    /**
     * @inheritdoc
     */
    protected function validateEmail(Request $request)
    {
        $this->validate($request, ['email' => 'required|email']);

        if (!$this->userService->checkEmailAccess($request->get('email'))) {
            $error = ValidationException::withMessages(['email' => $this->userService->getEmailAccessError($request->get('email'))]);
            throw $error;
        }

        if (!$this->userService->checkEmailAccessToModule($request->route()->getPrefix(), $request->get('email'), $request->companySlug())) {
            $error = ValidationException::withMessages(['email' => __('auth.not_exists_on_module')]);
            throw $error;
        }
    }
}
