<?php

namespace App\System\Http\Controllers;

use App\System\Services\SettingService;
use App\Services\TextService;
use App\System\Http\Requests\StoreSettingsRequest;
use App\System\Http\Resources\Collections\SettingCollection;

/**
 * Class SettingController
 *
 * @package App\System\Http\Controllers
 */
class SettingController extends Controller
{
    /** @var SettingService */
    private $settingService;
    /** @var TextService */
    private $textService;

    /**
     * Create a new controller instance.
     *
     * @param SettingService $settingService
     */
    public function __construct(
        SettingService $settingService,
        TextService $textService
    ) {
        parent::__construct();

        $this->settingService = $settingService;
        $this->textService = $textService;
    }

    /**
     * Get all system settings
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $settings = $this->settingService->all();
        $texts = $this->textService->all();

        return response()->success((new SettingCollection($settings))->texts($texts));
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

        return response()->success(new SettingCollection($settings));
    }
}
