<?php

namespace App\Portal\Http\Controllers\V1;

use App\Helpers\StorageHelper;
use App\Portal\Http\Controllers\Controller;
use App\Portal\Services\SettingService;
use App\Portal\Http\Requests\V1\StoreSettingsRequest;
use App\Portal\Http\Resources\V1\Collections\SettingCollection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class SettingController
 *
 * @package App\Portal\Http\Controllers\V1
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


    /**
     * @return string
     */
    public function downloadLeasingablePdf()
    {
        $path = $this->settingService->getPathLeasingablePdfFile();

        if (!empty($path) && StorageHelper::exists($path, StorageHelper::PUBLIC_DISK)) {
            return StorageHelper::downloadFromDisk($path, StorageHelper::PUBLIC_DISK);
        }

        throw new NotFoundHttpException('File not found');
    }
}
