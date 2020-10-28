<?php

namespace App\Portal\Services\Base;

use App\Portal\Helpers\SettingHelper;
use App\Helpers\StorageHelper;
use App\Repositories\BaseRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

/**
 * Class SettingService
 *
 * @package App\Portal\Services\Base
 */
abstract class SettingService
{
    /** @var BaseRepository */
    protected $settingsRepository;

    /**
     * @param mixed $logo
     * @param string $name
     * @param null|string $folder
     *
     * @return Collection
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handleLogo($logo, string $name, ?string $folder = null): string
    {
        if (is_null($logo)) {
            // Delete existing logo file
            if (SettingHelper::logo()) {
                StorageHelper::delete(SettingHelper::logo(), StorageHelper::PUBLIC_DISK);
            }
            $logo = '';
        } elseif ($logo instanceof UploadedFile) {
            $logo = $logo->store('portals/logos', 'public');
        }

        return Storage::url($logo);
    }

    /**
     * @return Collection
     */
    abstract public function all(): array;

    /**
     * Publishes system settings to application config
     *
     * @return void
     */
    public function load(): void
    {
        config()->set(config('app.settings_key'), $this->all());
    }
}
