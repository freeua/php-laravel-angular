<?php

namespace App\Portal\Http\Controllers\V1\Company;

use App\Models\Companies\Company;
use App\Portal\Helpers\AuthHelper;
use App\Portal\Http\Controllers\Controller;
use App\Http\Requests\DefaultListRequest;
use App\Portal\Http\Requests\V1\Company\UpdateCompanyUserRequest;
use App\Portal\Http\Requests\V1\HomepageRequest;
use App\Portal\Http\Resources\V1\Company\CompanyUserResource;
use App\Portal\Http\Resources\V1\Company\ListCollections\UserListCollection;
use App\Portal\Http\Resources\V1\HomepageResource;
use App\Portal\Http\Resources\V1\UserResource;
use App\Portal\Http\Resources\V1\UserListResource;
use App\Portal\Models\Homepage;
use App\Portal\Models\User;
use App\Portal\Repositories\Company\UserRepository;
use App\Portal\Services\Company\UserService;
use App\Traits\UploadsFile;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class UserController
 *
 * @package App\Portal\Http\Controllers\V1\Company
 */
class UserController extends Controller
{
    use UploadsFile;
    /** @var UserService */
    private $userService;
    /** @var UserRepository */
    private $userRepository;

    /**
     * UserController constructor.
     *
     * @param UserService    $userService
     * @param UserRepository $userRepository
     */
    public function __construct(UserService $userService, UserRepository $userRepository)
    {
        parent::__construct();

        $this->userService = $userService;
        $this->userRepository = $userRepository;
    }

    /**
     * Returns list of users
     *
     * @param DefaultListRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(DefaultListRequest $request)
    {
        $users = $this->userRepository->list($request->validated());

        return response()->pagination(UserListResource::collection($users));
    }

    /**
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function view(User $user)
    {

        return response()->success(new CompanyUserResource($user->load(['audits'])));
    }

    /**
     * @param User $user
     * @param UpdateCompanyUserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(User $user, UpdateCompanyUserRequest $request)
    {

        $user = $this->userService->selfUpdate($user, $request->validated());

        return $user
            ? response()->success(new CompanyUserResource($user->load(['audits'])))
            : response()->error([__('user.update.failed')], __('user.update.failed'));
    }

    /**
     * Approve an user
     *
     * @param User              $user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function approve(User $user)
    {
        if (!$this->userService->canBeApprovedRejected($user)) {
            return response()->error([__('user.approve.invalid')], __('user.approve.invalid'), JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $result = $this->userService->approve($user->id);

        return $result
            ? response()->success(new UserResource($user->fresh()))
            : response()->error([__('user.approve.failed')], __('user.approve.failed'));
    }

    /**
     * Reject an user
     *
     * @param User              $user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function reject(User $user)
    {
        if (!$this->userService->canBeApprovedRejected($user)) {
            return response()->error([__('user.reject.invalid')], __('user.reject.invalid'), JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $result = $this->userService->reject($user->id);

        return $result
            ? response()->success(new UserResource($user->fresh()))
            : response()->error([__('user.reject.failed')], __('user.reject.failed'));
    }

    /**
     * Delete user
     *
     * @param User $user
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function delete(User $user)
    {
        return response()->error([__('user.delete.failed')], __('user.delete.failed'));
    }

    public function getHomepage()
    {
        $company = AuthHelper::company();
        if ($company->getEmployeeHomePage()) {
            return response()->success(new HomepageResource($company->getEmployeeHomePage()));
        } else {
            return response()->success(Homepage::getDefaultHomepageByType(Homepage::EMPLOYEE_DEFAULT_HOMEPAGE));
        }
    }

    public function updateHomepage(HomepageRequest $request)
    {
        $company = AuthHelper::company();
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
            'type' => Homepage::EMPLOYEE_HOMEPAGE
        ]);
        \Cache::forget(Homepage::EMPLOYEE_HOMEPAGE.'_'.$company->id);
        \DB::commit();

        return response()->success(new HomepageResource($company->getEmployeeHomePage()));
    }
}
