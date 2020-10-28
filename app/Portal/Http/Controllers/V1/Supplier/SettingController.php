<?php

namespace App\Portal\Http\Controllers\V1\Supplier;

use App\Helpers\StorageHelper;
use App\Portal\Http\Controllers\Controller;
use App\Portal\Services\Supplier\SettingService;
use App\Portal\Http\Requests\V1\Supplier\StoreSettingsRequest;
use App\Portal\Http\Resources\V1\Supplier\Collections\SettingCollection;
use App\Portal\Services\Supplier\UpdateSettingService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class SettingController
 *
 * @package App\Portal\Http\Controllers\V1\Supplier
 */
class SettingController extends Controller
{
    /** @var UpdateSettingService */
    private $updateSettingService;
    /** @var SettingService */
    private $settingService;

    /**
     * Create a new controller instance.
     *
     * @param SettingService       $settingService
     * @param UpdateSettingService $updateSettingService
     */
    public function __construct(SettingService $settingService, UpdateSettingService $updateSettingService)
    {
        parent::__construct();

        $this->settingService = $settingService;
        $this->updateSettingService = $updateSettingService;
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
        $settings = $this->updateSettingService->update($request->validated());

        return response()->success($settings);
    }
}
