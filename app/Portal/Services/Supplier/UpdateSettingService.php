<?php

namespace App\Portal\Services\Supplier;

use App\Portal\Helpers\AuthHelper;
use App\Portal\Repositories\SupplierRepository;
use App\Portal\Services\SupplierService;
use App\System\Repositories\SupplierRepository as SystemSupplierRepository;
use App\Portal\Repositories\Supplier\SettingRepository;
use App\Traits\UploadsFile;
use Illuminate\Support\Collection;

/**
 * Class SettingService
 *
 * @package App\Portal\Services\Supplier
 */
class UpdateSettingService
{
    use UploadsFile;
    /** @var SupplierRepository */
    private $settingService;
    /** @var SystemSupplierRepository */
    private $supplierService;
    /** @var SettingRepository */
    protected $settingsRepository;

    /**
     * UserService constructor.
     *
     * @param SettingRepository $settingsRepository
     * @param SettingService    $settingService
     * @param SupplierService   $supplierService
     */
    public function __construct(
        SettingRepository $settingsRepository,
        SettingService $settingService,
        SupplierService $supplierService
    ) {
        $this->settingsRepository = $settingsRepository;
        $this->settingService = $settingService;
        $this->supplierService = $supplierService;
    }

    /**
     * @param array $data
     *
     * @return Collection
     * @throws \Exception
     */
    public function update(array $data)
    {
        $supplierId = AuthHelper::supplierId();
        $supplierData = [
            'color' => $data['color'],
        ];

        if (isset($data['logo'])) {
            $supplierData['logo'] = UploadsFile::handleFile($data['logo'], "/suppliers/{$supplierId}/logos");
        }

        $this->supplierService->selfUpdateById(AuthHelper::supplierId(), $supplierData);

        return $this->settingService->all();
    }
}
