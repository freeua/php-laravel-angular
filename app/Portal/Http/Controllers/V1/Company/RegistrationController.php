<?php

namespace App\Portal\Http\Controllers\V1\Company;

use App\Portal\Http\Controllers\Controller;
use App\Portal\Http\Requests\V1\Company\RegisterRequest;
use App\Portal\Http\Requests\V1\Company\SendRegistrationLinkRequest;
use App\Portal\Services\Company\UserService;

/**
 * Class RegistrationController
 *
 * @package App\Portal\Http\Controllers\V1\Company
 */
class RegistrationController extends Controller
{
    /** @var UserService */
    private $userService;

    /**
     * UserController constructor.
     *
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        parent::__construct();

        $this->userService = $userService;
    }

    /**
     * Send email with registration link
     *
     * @param SendRegistrationLinkRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendLink(SendRegistrationLinkRequest $request)
    {
        $this->userService->sendRegistrationLink($request->get('email'), request()->companySlug());

        return response()->success([__('user.registration.send_link.success')], __('user.registration.send_link.success'));
    }

    /**
     * Register an user
     *
     * @param RegisterRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        $data['company_slug'] = request()->companySlug();

        $result = $this->userService->register($data);

        return $result
            ? response()->success([__('user.registration.register.success')], __('user.registration.register.success'))
            : response()->error([__('user.registration.register.failed')], __('user.registration.register.failed'));
    }
}
