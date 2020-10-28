<?php

namespace App\Portal\Http\Controllers\V1\Company;

use App\Portal\Helpers\AuthHelper;
use App\Portal\Http\Controllers\Controller;
use App\Portal\Services\Company\SettingService;
use App\Portal\Http\Requests\V1\Company\StoreSettingsRequest;
use App\Portal\Http\Resources\V1\Company\Collections\SettingCollection;
use App\Portal\Http\Resources\V1\Employee\Collections\SettingCollection as EmployeeSettingCollection;

/**
 * Class SettingController
 *
 * @package App\Portal\Http\Controllers\V1\Company
 */
class SettingController extends Controller
{
    /** @var SettingService */
    private $settingService;

    /**
     * Create a new controller instance.
     *
     * @param SettingService $settingService
     */
    public function __construct(SettingService $settingService)
    {
        parent::__construct();

        $this->settingService = $settingService;
    }

    /**
     * Get all system settings
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $settings = $this->settingService->all();

        return response()->success($settings);
    }

    /**
     * Store system settings
     *
     * @param StoreSettingsRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function update(StoreSettingsRequest $request)
    {
        $settings = $this->settingService->update($request->validated());

        return response()->success($settings);
    }
}
