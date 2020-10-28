<?php

namespace App\System\Listeners;

use App\System\Events\System\Verified;
use App\System\Services\SettingService;
use Illuminate\Events\Dispatcher;

/**
 * Class LoadSettings
 *
 * @package App\System\Listeners
 */
class LoadSettings
{
    /**
     * @var SettingService
     */
    protected $settingService;

    /**
     * LoadSettings constructor.
     *
     * @param SettingService $settingService
     */
    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
    }

    /**
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen([Verified::class], [$this, 'loadSettings']);
    }

    /**
     * Loads settings from db to application config
     */
    public function loadSettings()
    {
        $this->settingService->load();
    }
}
