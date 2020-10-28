<?php

namespace App\System\Services;

use App\Portal\Http\Requests\V1\UploadPortalFilesRequest;
use App\System\Repositories\SettingRepository;
use App\Helpers\SettingHelper;
use App\Helpers\StorageHelper;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use App\Traits\UploadsFile;

/**
 * Class SettingService
 *
 * @package App\System\Services
 */
class SettingService
{
    use UploadsFile;
    /** @var SettingRepository */
    private $settingsRepository;

    /**
     * UserService constructor.
     *
     * @param SettingRepository $settingsRepository
     */
    public function __construct(SettingRepository $settingsRepository)
    {
        $this->settingsRepository = $settingsRepository;
    }

    /**
     * @param array $data
     *
     * @return \Illuminate\Support\Collection
     * @throws \Exception
     */
    public function update(array $data)
    {
        if ($data['logo'] instanceof UploadedFile) {
            $data['logo'] = self::uploadLogo($data['logo']);
        } else {
            if (empty($data['logo'])) {
                if (SettingHelper::logo()) {
                    StorageHelper::disk('public')->delete(SettingHelper::logo());
                }
                $data['logo'] = '';
            }
        }

        if ($data['leasingable_pdf'] instanceof UploadedFile) {
            $data['leasingable_pdf'] = self::uploadLeasingablePdf($data['leasingable_pdf']);
        } else {
            if (empty($data['leasingable_pdf'])) {
                if (SettingHelper::leasingablePdf()) {
                    StorageHelper::disk('public')->delete(SettingHelper::leasingablePdf());
                }
                $data['leasingable_pdf'] = '';
            }
        }

        \DB::beginTransaction();
        $this->settingsRepository->deleteAll();

        foreach ($data as $key => $value) {
            $this->settingsRepository->create($key, $value);
        }
        \DB::commit();

        return $this->settingsRepository->all();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
        return $this->settingsRepository->all();
    }

    /**
     * Publishes system settings to application config
     */
    public function load()
    {
        $settings = $this->settingsRepository->all();
        config()->set(config('app.settings_key'), $settings->pluck('value', 'key'));
    }

    private function uploadLogo(UploadedFile $request)
    {
        $logo = UploadsFile::handleFile($request, "/system-admin", SettingHelper::logo());
        return  $logo;
    }

    private function uploadLeasingablePdf(UploadedFile $request)
    {
        $leasingablePdf = UploadsFile::handleFile($request, "/system-admin", SettingHelper::leasingablePdf());
        return  $leasingablePdf;
    }
}
