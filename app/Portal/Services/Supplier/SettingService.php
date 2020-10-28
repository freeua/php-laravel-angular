<?php

namespace App\Portal\Services\Supplier;

use App\Helpers\PortalHelper;
use App\Portal\Helpers\AuthHelper;
use App\Helpers\TextHelper;
use App\Portal\Models\Supplier;
use App\Models\Text;
use App\Portal\Repositories\SupplierRepository;
use App\Portal\Repositories\Supplier\SettingRepository;
use App\Repositories\TextRepository;
use Illuminate\Support\Collection;

/**
 * Class SettingService
 *
 * @package App\Portal\Services\Supplier
 */
class SettingService extends \App\Portal\Services\Base\SettingService
{
    /** @var SupplierRepository */
    private $supplierRepository;
    /** @var SettingRepository */
    protected $settingsRepository;
    /** @var TextRepository */
    protected $textRepository;

    /**
     * UserService constructor.
     *
     * @param SettingRepository        $settingsRepository
     * @param SupplierRepository       $supplierRepository
     */
    public function __construct(
        SettingRepository $settingsRepository,
        SupplierRepository $supplierRepository,
        TextRepository $textRepository
    ) {
        $this->settingsRepository = $settingsRepository;
        $this->supplierRepository = $supplierRepository;
        $this->textRepository = $textRepository;
    }

    /**
     * @return Collection
     */
    public function all(): array
    {
        $supplier = $this->supplierRepository->find(AuthHelper::supplierId());
        $data = [
            'id' => PortalHelper::id(),
            'name' => $supplier->name,
            'vat' => $supplier->vat,
            'admin_email' => $supplier->admin_email,
            'phone' => $supplier->phone,
            'logo' => $supplier->logo,
            'color' => $supplier->color,
            'shop_name' => $supplier->shop_name,
            'blind_discount' => $supplier->getBlindDiscount(PortalHelper::id()),
            'gp_number' => $supplier->gp_number,
            'bank_account' => $supplier->bank_account,
            'grefo' => $supplier->grefo,
            'bank_name' => $supplier->bank_name,
            'texts' => $this->getTextSettingsCollection(),
        ];

        return $data;
    }

    private function getTextSettingsCollection()
    {
        $textCollection = \Cache::rememberForever(Text::getCacheCollectionKey(), function () {
            return TextHelper::getSettingsCollection($this->textRepository->all());
        });
        return $textCollection;
    }

    /**
     * @param Supplier $supplier
     *
     * @return void
     */
    public function addDefaultSettings(Supplier $supplier): void
    {
        $settings = [
            'logo'      => '',
            'color'     => '#80e5b1',
            'shop_name' => $supplier->name
        ];

        foreach ($settings as $key => $value) {
            $this->settingsRepository->create($supplier->id, $key, $value);
        }
    }
}
