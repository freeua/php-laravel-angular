<?php

namespace App\Portal\Http\Controllers\V1\Supplier;

use App\Exceptions\PendingUserException;
use App\Exceptions\RejectedUserException;
use App\Exceptions\UserNotFoundException;
use App\Models\Companies\Company;
use App\Portal\Http\Controllers\Controller;
use App\Http\Requests\DefaultListRequest;
use App\Portal\Http\Requests\V\Supplier\UpdateUserRequest;
use App\Portal\Http\Requests\V1\Supplier\CreateUserRequest;
use App\Portal\Http\Requests\V1\Supplier\SearchUserRequest;
use App\Portal\Http\Resources\V1\Supplier\ListCollections\UserListCollection;
use App\Portal\Http\Resources\V1\UserResource;
use App\Portal\Models\User;
use App\Portal\Repositories\Supplier\UserRepository;
use App\Portal\Services\Supplier\UserService;

/**
 * Class UserController
 *
 * @package App\Portal\Http\Controllers\V1\Supplier
 */
class UserController extends Controller
{
    /** @var UserService */
    private $userService;
    /** @var UserRepository */
    private $userRepository;

    public function __construct(UserRepository $userRepository, UserService $userService)
    {
        parent::__construct();

        $this->userRepository = $userRepository;
        $this->userService = $userService;
    }

    public function index(DefaultListRequest $request)
    {
        $users = $this->userRepository->list($request->validated());

        return response()->success(new UserListCollection($users));
    }

    public function create(CreateUserRequest $request)
    {
        $user = $this->userService->selfCreate($request->validated());

        return $user
            ? response()->success(new UserResource($user))
            : response()->error([__('user.create.failed')], __('user.create.failed'));
    }

    public function update(User $user, UpdateUserRequest $request)
    {
        $user = $this->userService->selfUpdate($user, $request->validated());

        return $user
            ? response()->success(new UserResource($user))
            : response()->error([__('user.update.failed')], __('user.update.failed'));
    }

    public function view(User $user)
    {
        return response()->success(new UserResource($user));
    }

    public function search(SearchUserRequest $request)
    {
        try {
            $user = $this->userService->search($request->validated());
            return response()->json($user);
        } catch (UserNotFoundException $ex) {
            return response()->error(['message' => 'User not found'], 'User not found', 404);
        }
    }
}
