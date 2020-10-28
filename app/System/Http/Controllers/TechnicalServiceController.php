<?php

namespace App\System\Http\Controllers;

use App\Http\Requests\DefaultListRequest;
use App\Modules\TechnicalServices\Models\TechnicalService;
use App\Modules\TechnicalServices\Resources\TechnicalServiceResource;
use App\Portal\Http\Resources\V1\ListCollections\TechnicalServiceListCollection;
use App\Portal\Http\Resources\V1\TechnicalServiceEmployeeUserResource;
use App\Portal\Models\User;
use App\System\Http\Resources\UserResource;
use App\System\Repositories\TechnicalServiceRepository;
use App\System\Repositories\UserRepository;

/**
 * Class TechnicalServiceController
 *
 * @package App\System\Http\Controllers
 */
class TechnicalServiceController extends Controller
{
    /** @var TechnicalServiceRepository */
    private $technicalServiceRepository;
    /** @var UserRepository */
    private $userRepository;

    /**
     * TechnicalServiceController constructor.
     *
     * @param TechnicalServiceRepository $technicalServiceRepository
     * @param UserRepository $userRepository
     */
    public function __construct(TechnicalServiceRepository $technicalServiceRepository, UserRepository $userRepository)
    {
        parent::__construct();

        $this->technicalServiceRepository = $technicalServiceRepository;
        $this->userRepository = $userRepository;
    }


    public function index(DefaultListRequest $request)
    {
        $technicalServices = $this->technicalServiceRepository->list($request->validated());
        return response()->pagination(TechnicalServiceListCollection::collection($technicalServices));
    }

    public function view(TechnicalService $technicalService)
    {
        return response()->json(new TechnicalServiceResource(
            $technicalService->load('order', 'offer', 'contract', 'audits')
        ));
    }

    public function employees(DefaultListRequest $request)
    {
        $employees = $this->userRepository->findAllEmployees($request->validated());

        return response()->pagination(UserResource::collection($employees));
    }

    public function employee(User $employee)
    {
        return response()->json(new TechnicalServiceEmployeeUserResource($employee->load(['audits'])));
    }

    public function employeeTechnicalServices(User $employee, DefaultListRequest $request)
    {
        $technicalServices = $this->technicalServiceRepository->listForEmployee($employee->id, $request->validated());
        return response()->pagination(TechnicalServiceListCollection::collection($technicalServices));
    }
}
