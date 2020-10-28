<?php

namespace App\Portal\Listeners;

use App\Portal\Helpers\AuthHelper;
use App\Portal\Events\Portal\Verified;
use App\Portal\Models\Role;
use Illuminate\Events\Dispatcher;

/**
 * Class LoadSettings
 *
 * @package App\Portal\Listeners
 */
class LoadSettings
{
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
        switch (AuthHelper::role()) {
            case Role::ROLE_COMPANY_ADMIN:
            case Role::ROLE_EMPLOYEE:
                $settingsClass = \App\Portal\Services\Company\SettingService::class;
                break;
            case Role::ROLE_SUPPLIER_ADMIN:
                $settingsClass = \App\Portal\Services\Supplier\SettingService::class;
                break;
            case Role::ROLE_PORTAL_ADMIN:
            default:
                $settingsClass = \App\Portal\Services\SettingService::class;
        }

        app()->make($settingsClass)->load();
    }
}
