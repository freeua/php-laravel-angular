<?php

namespace App\Portal\Services;

use App\Helpers\PortalHelper;
use App\Portal\Helpers\SettingHelper;
use App\Helpers\TextHelper;
use App\Models\Text;
use App\System\Repositories\PortalLeasingSettingRepository;
use App\System\Repositories\PortalRepository;
use App\Repositories\TextRepository;
use Illuminate\Support\Collection;

/**
 * Class SettingService
 *
 * @package App\Portal\Services
 */
class SettingService extends Base\SettingService
{
    /** @var PortalLeasingSettingRepository */
    private $leasingSettingRepository;
    /** @var PortalRepository */
    private $portalRepository;
    /** @var TextRepository */
    private $textRepository;

    /**
     * UserService constructor.
     *
     * @param PortalRepository               $portalRepository
     * @param PortalLeasingSettingRepository $leasingSettingRepository
     */
    public function __construct(
        PortalRepository $portalRepository,
        PortalLeasingSettingRepository $leasingSettingRepository,
        TextRepository $textRepository
    ) {
        $this->portalRepository = $portalRepository;
        $this->leasingSettingRepository = $leasingSettingRepository;
        $this->textRepository = $textRepository;
    }

    /**
     * @param array $data
     *
     * @return Collection
     * @throws \Exception
     */
    public function update(array $data): array
    {
        $leasingSettingsData = $data['leasing_settings'];
        unset($data['leasing_settings']);

        $portalData = [
            'admin_first_name'  => $data['admin_first_name'],
            'color'             => $data['color'],
            'admin_last_name'   => $data['admin_last_name'],
            'company_name'      => $data['company_name'],
            'company_zip'       => $data['company_zip'],
            'company_city_id'   => $data['company_city_id'],
            'company_address'   => $data['company_address'],
            'company_vat'       => $data['company_vat'],
            'status_id'         => $data['status_id']
        ];

        $this->leasingSettingRepository->updateAllByPortalId(PortalHelper::id(), $leasingSettingsData);



        if (isset($data['logo'])) {
            $portalData['logo'] = $this->handleLogo($data['logo'], 'logo', '/portals/' . PortalHelper::id());
        }

        $this->portalRepository->update(PortalHelper::id(), $portalData);
        return $this->all();
    }

    /**
     * @return Collection
     */
    public function all(): array
    {
        $portal = PortalHelper::getPortal();

        $data['id'] = $portal->id;
        $data['name'] = $portal->name;
        $data['domain'] = $portal->domain;
        $data['logo'] = $portal->logo;
        $data['color'] = $portal->color;
        $data['status'] = $portal->status;
        $data['mainDomain'] = env('APP_URL_BASE');
        $data['leasingablePdf'] = SettingHelper::leasingablePdf();
        $data['texts'] = $this->getTextSettingsCollection();

        return $data;
    }

    private function getTextSettingsCollection()
    {
        $textCollection = \Cache::rememberForever(Text::getCacheCollectionKey(), function () {
            return TextHelper::getSettingsCollection($this->textRepository->all());
        });
        return $textCollection;
    }

    public function getPathLeasingablePdfFile()
    {
        $leasingablePdf = PortalHelper::getPortal()->leasingable_pdf;

        return !empty($leasingablePdf) ? $leasingablePdf : SettingHelper::leasingablePdf();
    }
}
