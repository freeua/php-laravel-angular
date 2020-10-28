<?php

namespace App\System\Http\Controllers;

use App\System\Exports\UsersExport;
use App\System\Http\Requests\CreateUserRequest;
use App\Http\Requests\DefaultListRequest;
use App\System\Http\Requests\UpdateUserRequest;
use App\System\Http\Resources\ListCollections\UserListCollection;
use App\System\Http\Resources\UserResource;
use App\System\Models\User;
use App\System\Repositories\UserRepository;
use App\System\Services\UserService;
use Maatwebsite\Excel\Excel;

/**
 * Class UserController
 *
 * @package App\System\Http\Controllers
 */
class UserController extends Controller
{
    /** @var UserService */
    private $userService;
    /** @var UserRepository */
    private $userRepository;

    /**
     * UserController constructor.
     *
     * @param UserService $userService
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
     * @throws \Exception
     */
    public function index(DefaultListRequest $request)
    {
        $users = $this->userRepository->list($request->validated());

        return response()->pagination(UserResource::collection($users));
    }

    /**
     * Export list of users
     *
     * @param Excel       $excel
     * @param UsersExport $export
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function export(Excel $excel, UsersExport $export)
    {
        return $excel->download($export, 'Users.xlsx', Excel::XLSX);
    }

    /**
     * Create a new user
     *
     * @param CreateUserRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function create(CreateUserRequest $request)
    {
        $user = $this->userService->create($request->validated());

        return $user
            ? response()->success(new UserResource($user))
            : response()->error([__('user.create.failed')], __('user.create.failed'));
    }

    /**
     * Update user data
     *
     * @param User $user
     * @param UpdateUserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(User $user, UpdateUserRequest $request)
    {
        $user = $this->userService->update($user, $request->validated());

        return $user
            ? response()->success(new UserResource($user))
            : response()->error([__('user.update.failed')], __('user.update.failed'));
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
        $result = $this->userRepository->deleteModel($user);

        return $result
            ? response()->success()
            : response()->error([__('user.delete.failed')], __('user.delete.failed'));
    }

    /**
     * View user
     *
     * @param User $user
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function view(User $user)
    {
        return response()->success(new UserResource($user));
    }
}
